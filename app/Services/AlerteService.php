<?php

namespace App\Services;

use App\Enums\AlerteStatut;
use App\Enums\AlerteType;
use App\Enums\ConteneurStatut;
use App\Enums\DocumentCategorie;
use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Enums\DossierStatut;
use App\Enums\LivraisonStatut;
use App\Models\Alerte;
use App\Models\Conteneur;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\Livraison;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class AlerteService
{
    public const SEUIL_ETA_JOURS = 3;

    public const SEUIL_RETARD_JOURS = 2;

    public const SEUIL_ECHEANCE_JOURS = 5;

    public const CATEGORIES_REQUISES = [
        DocumentCategorie::FactureCommerciale,
        DocumentCategorie::PackingList,
        DocumentCategorie::BillOfLading,
    ];

    /** Types d'alerte qui déclenchent une notification au client (§17). */
    private const TYPES_NOTIFIABLES = [
        AlerteType::FactureImpayee,
        AlerteType::EcheanceImportante,
    ];

    public function __construct(
        private readonly SoldeService $solde,
        private readonly NotificationService $notifications,
    ) {}

    public function genererToutes(): int
    {
        return array_sum([
            $this->arriveeNavire(),
            $this->dossierIncomplet(),
            $this->documentManquant(),
            $this->dossierBloque(),
            $this->factureImpayee(),
            $this->livraisonAEffectuer(),
            $this->conteneurARetourner(),
            $this->echeanceImportante(),
            $this->retardDossier(),
        ]);
    }

    public function arriveeNavire(): int
    {
        $conteneurs = Conteneur::query()
            ->where('statut', ConteneurStatut::EnAttente->value)
            ->whereBetween('date_eta', [Carbon::today(), Carbon::today()->addDays(self::SEUIL_ETA_JOURS)])
            ->get();

        return $this->compter($conteneurs, fn (Conteneur $conteneur) => $this->creer(
            $conteneur->dossier,
            AlerteType::ArriveeNavire,
            "Arrivée prochaine du navire : conteneur {$conteneur->numero}, ETA {$conteneur->date_eta->format('d/m/Y')}.",
        ));
    }

    public function dossierIncomplet(): int
    {
        $dossiers = Dossier::query()
            ->where('statut', DossierStatut::DocumentsRecus->value)
            ->whereDoesntHave('documents')
            ->get();

        return $this->compter($dossiers, fn (Dossier $dossier) => $this->creer(
            $dossier,
            AlerteType::DossierIncomplet,
            "Dossier incomplet : aucun document reçu pour #{$dossier->numero}.",
        ));
    }

    public function documentManquant(): int
    {
        $dossiers = Dossier::query()
            ->whereIn('statut', [
                DossierStatut::DocumentsRecus->value,
                DossierStatut::EnCours->value,
                DossierStatut::Dedouanement->value,
            ])
            ->get();

        $crees = 0;

        foreach ($dossiers as $dossier) {
            $categories = $dossier->documents->pluck('categorie');

            foreach (self::CATEGORIES_REQUISES as $requise) {
                if (! $categories->contains($requise)) {
                    $crees += (int) $this->creer(
                        $dossier,
                        AlerteType::DocumentManquant,
                        "Document manquant : {$requise->value} pour #{$dossier->numero}.",
                    );
                }
            }
        }

        return $crees;
    }

    public function dossierBloque(): int
    {
        $dossiers = Dossier::query()->where('bloque', true)->get();

        return $this->compter($dossiers, fn (Dossier $dossier) => $this->creer(
            $dossier,
            AlerteType::DossierBloque,
            "Dossier bloqué : {$dossier->raison_blocage}.",
        ));
    }

    public function factureImpayee(): int
    {
        $crees = 0;

        foreach ($this->facturesEmises() as $facture) {
            $echeance = $facture->date_echeance;

            if ($echeance !== null && $echeance->isPast() && $this->solde->resteParFacture($facture) > 0) {
                $crees += (int) $this->creer(
                    $facture->dossier,
                    AlerteType::FactureImpayee,
                    "Facture impayée : {$facture->numero} (échéance {$echeance->format('d/m/Y')}).",
                    'document_commercial',
                    $facture->id,
                    $echeance,
                );
            }
        }

        return $crees;
    }

    public function livraisonAEffectuer(): int
    {
        $livraisons = Livraison::query()
            ->where('statut', LivraisonStatut::Planifiee->value)
            ->where('date_heure_chargement', '<=', Carbon::today()->endOfDay())
            ->get();

        return $this->compter($livraisons, fn (Livraison $livraison) => $this->creer(
            $livraison->dossier,
            AlerteType::LivraisonAEffectuer,
            "Livraison à effectuer : {$livraison->destination} le {$livraison->date_heure_chargement->format('d/m/Y H:i')}.",
            'livraison',
            $livraison->id,
        ));
    }

    public function conteneurARetourner(): int
    {
        $conteneurs = Conteneur::query()
            ->where('statut', ConteneurStatut::Livre->value)
            ->whereNull('date_retour')
            ->get();

        return $this->compter($conteneurs, fn (Conteneur $conteneur) => $this->creer(
            $conteneur->dossier,
            AlerteType::ConteneurARetourner,
            "Conteneur à retourner : {$conteneur->numero}.",
            'conteneur',
            $conteneur->id,
        ));
    }

    public function echeanceImportante(): int
    {
        $crees = 0;

        foreach ($this->facturesEmises() as $facture) {
            $echeance = $facture->date_echeance;

            if ($echeance !== null
                && $echeance->between(Carbon::today(), Carbon::today()->addDays(self::SEUIL_ECHEANCE_JOURS))
                && $this->solde->resteParFacture($facture) > 0) {
                $crees += (int) $this->creer(
                    $facture->dossier,
                    AlerteType::EcheanceImportante,
                    "Échéance importante : facture {$facture->numero} le {$echeance->format('d/m/Y')}.",
                    'document_commercial',
                    $facture->id,
                    $echeance,
                );
            }
        }

        return $crees;
    }

    public function retardDossier(): int
    {
        $dossiers = Dossier::query()
            ->whereNot('statut', DossierStatut::Cloture->value)
            ->where('date_arrivee_prevue', '<', Carbon::today()->subDays(self::SEUIL_RETARD_JOURS))
            ->get();

        return $this->compter($dossiers, fn (Dossier $dossier) => $this->creer(
            $dossier,
            AlerteType::RetardDossier,
            "Retard du dossier #{$dossier->numero} : arrivée prévue le {$dossier->date_arrivee_prevue->format('d/m/Y')}.",
        ));
    }

    private function facturesEmises()
    {
        return DocumentCommercial::query()
            ->where('type', DocumentCommercialType::Facture->value)
            ->whereIn('statut', [
                DocumentCommercialStatut::Emis->value,
                DocumentCommercialStatut::PartiellementPaye->value,
            ])
            ->whereNotNull('date_echeance')
            ->get();
    }

    private function creer(
        ?Dossier $dossier,
        AlerteType $type,
        string $message,
        ?string $refType = null,
        ?int $refId = null,
        ?CarbonInterface $dateEcheance = null,
    ): bool {
        $query = Alerte::query()
            ->where('type', $type->value)
            ->whereIn('statut', [
                AlerteStatut::Nouvelle->value,
                AlerteStatut::Lue->value,
            ]);

        $query = $dossier !== null
            ? $query->where('dossier_id', $dossier->id)
            : $query->whereNull('dossier_id');

        $query = $refId !== null
            ? $query->where('ref_type', $refType)->where('ref_id', $refId)
            : $query->whereNull('ref_id');

        if ($query->exists()) {
            return false;
        }

        Alerte::create([
            'type' => $type,
            'message' => $message,
            'dossier_id' => $dossier?->id,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'date_echeance' => $dateEcheance,
            'statut' => AlerteStatut::Nouvelle,
        ]);

        if (in_array($type, self::TYPES_NOTIFIABLES, true) && $dossier?->client !== null) {
            $this->notifications->mettreEnFile(
                $dossier->client,
                (string) str($message)->before(' : '),
                $message,
            );
        }

        return true;
    }

    private function compter(iterable $elements, callable $callback): int
    {
        $crees = 0;

        foreach ($elements as $element) {
            $crees += (int) $callback($element);
        }

        return $crees;
    }
}

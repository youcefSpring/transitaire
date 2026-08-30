<?php

namespace App\Services;

use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Enums\FraisSens;
use App\Enums\TypeOperation;
use App\Models\AuditLog;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\Frai;
use App\Models\Paiement;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use InvalidArgumentException;

class RapportService
{
    public const TYPES = [
        'dossiers_periode', 'importations', 'exportations', 'chiffre_affaires', 'depenses',
        'benefices', 'impayes', 'paiements', 'activite_employes', 'activite_client',
        'activite_port', 'activite_type_transport', 'rentabilite_dossier',
    ];

    public function __construct(
        private readonly TauxChangeService $tauxChange,
        private readonly MargeService $marges,
        private readonly SoldeService $solde,
    ) {}

    public function generer(string $type, CarbonInterface $du, CarbonInterface $au): array
    {
        return match ($type) {
            'dossiers_periode' => $this->dossiersPeriode(null, $du, $au),
            'importations' => $this->dossiersPeriode(TypeOperation::Import, $du, $au),
            'exportations' => $this->dossiersPeriode(TypeOperation::Export, $du, $au),
            'chiffre_affaires' => $this->chiffreAffaires($du, $au),
            'depenses' => $this->depenses($du, $au),
            'benefices' => $this->benefices($du, $au),
            'impayes' => $this->impayes($du, $au),
            'paiements' => $this->paiements($du, $au),
            'activite_employes' => $this->activiteEmployes($du, $au),
            'activite_client' => $this->activiteGroupee('client_id', $du, $au),
            'activite_port' => $this->activiteGroupee('port_aeroport', $du, $au),
            'activite_type_transport' => $this->activiteGroupee('mode_transport', $du, $au),
            'rentabilite_dossier' => $this->rentabiliteDossier($du, $au),
            default => throw new InvalidArgumentException("Type de rapport inconnu : {$type}."),
        };
    }

    private function dossiersPeriode(?TypeOperation $type, CarbonInterface $du, CarbonInterface $au): array
    {
        $query = Dossier::query()
            ->with('client')
            ->whereBetween('created_at', [$du->startOfDay(), $au->endOfDay()])
            ->orderBy('numero');

        if ($type !== null) {
            $query->where('type', $type->value);
        }

        $dossiers = $query->get();

        return [
            'total' => $dossiers->count(),
            'lignes' => $dossiers->map(fn (Dossier $dossier) => [
                'numero' => $dossier->numero,
                'client' => $dossier->client?->raison_sociale,
                'type' => $dossier->type->value,
                'mode_transport' => $dossier->mode_transport->value,
                'statut' => $dossier->statut->value,
                'cree_le' => $dossier->created_at?->format('d/m/Y'),
            ])->all(),
        ];
    }

    private function chiffreAffaires(CarbonInterface $du, CarbonInterface $au): array
    {
        $factures = $this->facturesPeriode($du, $au);
        $total = 0.0;

        $lignes = $factures->map(function (DocumentCommercial $facture) use (&$total) {
            $montantDzd = $this->tauxChange->convertirEnDzd((float) $facture->montant_total, $facture->devise, $facture->date_emission);
            $total += $montantDzd;

            return [
                'numero' => $facture->numero,
                'client' => $facture->client?->raison_sociale,
                'devise' => $facture->devise->value,
                'montant' => (float) $facture->montant_total,
                'montant_dzd' => $montantDzd,
                'emise_le' => $facture->date_emission?->format('d/m/Y'),
            ];
        })->all();

        return ['total_dzd' => round($total, 2), 'lignes' => $lignes];
    }

    private function depenses(CarbonInterface $du, CarbonInterface $au): array
    {
        $frais = Frai::query()
            ->with(['dossier', 'fournisseur'])
            ->where('sens', FraisSens::SupporteTransitaire->value)
            ->whereBetween('date_frais', [$du->toDateString(), $au->toDateString()])
            ->orderBy('date_frais')
            ->get();

        $total = 0.0;

        $lignes = $frais->map(function (Frai $frai) use (&$total) {
            $montantDzd = $this->tauxChange->convertirEnDzd((float) $frai->montant, $frai->devise, $frai->date_frais);
            $total += $montantDzd;

            return [
                'dossier' => $frai->dossier?->numero,
                'categorie' => $frai->categorie->value,
                'fournisseur' => $frai->fournisseur?->nom,
                'montant_dzd' => $montantDzd,
                'date' => $frai->date_frais->format('d/m/Y'),
            ];
        })->all();

        return ['total_dzd' => round($total, 2), 'lignes' => $lignes];
    }

    private function benefices(CarbonInterface $du, CarbonInterface $au): array
    {
        $lignes = [];
        $mois = CarbonImmutable::instance($du->startOfMonth());

        while ($mois->lte($au->endOfMonth())) {
            $debut = $mois->startOfMonth();
            $fin = $mois->endOfMonth();

            $ca = $this->chiffreAffaires($debut, $fin)['total_dzd'];
            $depenses = $this->depenses($debut, $fin)['total_dzd'];

            $lignes[] = [
                'mois' => $mois->format('m/Y'),
                'chiffre_affaires_dzd' => $ca,
                'depenses_dzd' => $depenses,
                'benefice_dzd' => round($ca - $depenses, 2),
            ];

            $mois = $mois->addMonth();
        }

        return ['lignes' => $lignes];
    }

    private function impayes(CarbonInterface $du, CarbonInterface $au): array
    {
        $factures = $this->facturesPeriode($du, $au);
        $total = 0.0;

        $lignes = [];

        foreach ($factures as $facture) {
            $reste = $this->solde->resteParFacture($facture);

            if ($reste > 0) {
                $total += $reste;

                $lignes[] = [
                    'numero' => $facture->numero,
                    'client' => $facture->client?->raison_sociale,
                    'echeance' => $facture->date_echeance?->format('d/m/Y'),
                    'devise' => $facture->devise->value,
                    'montant' => (float) $facture->montant_total,
                    'reste' => $reste,
                ];
            }
        }

        return ['total_reste' => $total, 'lignes' => $lignes];
    }

    private function paiements(CarbonInterface $du, CarbonInterface $au): array
    {
        $paiements = Paiement::query()
            ->with('client')
            ->whereBetween('date_paiement', [$du->toDateString(), $au->toDateString()])
            ->orderBy('date_paiement')
            ->get();

        $total = 0.0;

        $lignes = $paiements->map(function (Paiement $paiement) use (&$total) {
            $montantDzd = $this->tauxChange->convertirEnDzd((float) $paiement->montant, $paiement->devise, $paiement->date_paiement);
            $total += $montantDzd;

            return [
                'client' => $paiement->client?->raison_sociale,
                'mode' => $paiement->mode->value,
                'devise' => $paiement->devise->value,
                'montant' => (float) $paiement->montant,
                'montant_dzd' => $montantDzd,
                'date' => $paiement->date_paiement->format('d/m/Y'),
            ];
        })->all();

        return ['total_dzd' => round($total, 2), 'lignes' => $lignes];
    }

    private function activiteEmployes(CarbonInterface $du, CarbonInterface $au): array
    {
        $lignes = AuditLog::query()
            ->with('user')
            ->whereBetween('created_at', [$du->startOfDay(), $au->endOfDay()])
            ->get()
            ->groupBy(fn (AuditLog $log) => $log->user?->name ?? 'Inconnu')
            ->map(fn ($logs, $utilisateur) => ['utilisateur' => $utilisateur, 'actions' => $logs->count()])
            ->values()
            ->all();

        return ['lignes' => $lignes];
    }

    private function activiteGroupee(string $colonne, CarbonInterface $du, CarbonInterface $au): array
    {
        $lignes = Dossier::query()
            ->whereBetween('created_at', [$du->startOfDay(), $au->endOfDay()])
            ->get()
            ->groupBy($colonne)
            ->map(fn ($dossiers, $libelle) => ['libelle' => $libelle, 'dossiers' => $dossiers->count()])
            ->values()
            ->all();

        return ['lignes' => $lignes];
    }

    private function rentabiliteDossier(CarbonInterface $du, CarbonInterface $au): array
    {
        $dossiers = Dossier::query()
            ->with('client')
            ->whereBetween('created_at', [$du->startOfDay(), $au->endOfDay()])
            ->orderBy('numero')
            ->get();

        $total = 0.0;

        $lignes = $dossiers->map(function (Dossier $dossier) use (&$total) {
            $marge = $this->marges->margeDossier($dossier)['dzd']['marge_reelle'];
            $total += $marge;

            return [
                'numero' => $dossier->numero,
                'client' => $dossier->client?->raison_sociale,
                'marge_dzd' => $marge,
            ];
        })->all();

        return ['total_marge_dzd' => round($total, 2), 'lignes' => $lignes];
    }

    private function facturesPeriode(CarbonInterface $du, CarbonInterface $au)
    {
        return DocumentCommercial::query()
            ->with('client')
            ->where('type', DocumentCommercialType::Facture->value)
            ->whereNotIn('statut', [
                DocumentCommercialStatut::Brouillon->value,
                DocumentCommercialStatut::Annule->value,
            ])
            ->whereBetween('date_emission', [$du->toDateString(), $au->toDateString()])
            ->orderBy('date_emission')
            ->get();
    }
}

<?php

namespace App\Services;

use App\Enums\ConteneurStatut;
use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Enums\DossierStatut;
use App\Enums\FraisSens;
use App\Enums\LivraisonStatut;
use App\Enums\ModeTransport;
use App\Models\Conteneur;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\Frai;
use App\Models\Livraison;
use App\Models\Paiement;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private readonly TauxChangeService $tauxChange,
    ) {}

    public function indicateurs(): array
    {
        return [
            'dossiers_en_cours' => Dossier::query()
                ->whereNotIn('statut', [DossierStatut::Nouveau->value, DossierStatut::Cloture->value])
                ->count(),
            'conteneurs_en_attente' => Conteneur::query()
                ->where('statut', ConteneurStatut::EnAttente->value)
                ->count(),
            'expeditions_aeriennes' => Dossier::query()
                ->where('mode_transport', ModeTransport::Aerien->value)
                ->whereIn('statut', [DossierStatut::EnCours->value, DossierStatut::Dedouanement->value])
                ->count(),
            'livraisons_du_jour' => Livraison::query()
                ->whereIn('statut', [LivraisonStatut::Planifiee->value, LivraisonStatut::EnCours->value])
                ->whereDate('date_heure_chargement', Carbon::today())
                ->count(),
            'chiffre_affaires' => $this->totalFactureDzd(Carbon::today()->startOfMonth()),
            'paiements_recus' => $this->totalPayeDzd(Carbon::today()->startOfMonth()),
            'impayes' => round($this->totalFactureDzd(null) - $this->totalPayeDzd(null), 2),
            'benefice_marge' => $this->margeDzd(),
            'dossiers_bloques' => Dossier::query()->where('bloque', true)->count(),
            'documents_manquants' => $this->documentsManquants(),
            'derniers_dossiers' => Dossier::query()
                ->with('client')
                ->latest()
                ->limit(8)
                ->get(),
        ];
    }

    private function totalFactureDzd(?Carbon $debut): float
    {
        $query = DocumentCommercial::query()
            ->where('type', DocumentCommercialType::Facture->value)
            ->whereNotIn('statut', [
                DocumentCommercialStatut::Brouillon->value,
                DocumentCommercialStatut::Annule->value,
            ]);

        if ($debut !== null) {
            $query->where('date_emission', '>=', $debut->toDateString());
        }

        $total = 0.0;

        foreach ($query->get() as $facture) {
            $total += $this->tauxChange->convertirEnDzd((float) $facture->montant_total, $facture->devise, $facture->date_emission);
        }

        return round($total, 2);
    }

    private function totalPayeDzd(?Carbon $debut): float
    {
        $query = Paiement::query();

        if ($debut !== null) {
            $query->where('date_paiement', '>=', $debut->toDateString());
        }

        $total = 0.0;

        foreach ($query->get() as $paiement) {
            $total += $this->tauxChange->convertirEnDzd((float) $paiement->montant, $paiement->devise, $paiement->date_paiement);
        }

        return round($total, 2);
    }

    private function margeDzd(): float
    {
        $marge = 0.0;

        foreach (Frai::query()->get() as $frai) {
            $montant = $this->tauxChange->convertirEnDzd((float) $frai->montant, $frai->devise, $frai->date_frais);

            $marge += $frai->sens === FraisSens::FactureClient ? $montant : -$montant;
        }

        return round($marge, 2);
    }

    private function documentsManquants(): int
    {
        $dossiers = Dossier::query()
            ->whereIn('statut', [
                DossierStatut::DocumentsRecus->value,
                DossierStatut::EnCours->value,
                DossierStatut::Dedouanement->value,
            ])
            ->with('documents')
            ->get();

        $manquants = 0;

        foreach ($dossiers as $dossier) {
            $categories = $dossier->documents->pluck('categorie');

            foreach (AlerteService::CATEGORIES_REQUISES as $requise) {
                if (! $categories->contains($requise)) {
                    $manquants++;
                }
            }
        }

        return $manquants;
    }
}

<?php

namespace App\Services;

use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Models\Client;
use App\Models\DocumentCommercial;

class SoldeService
{
    public function __construct(
        private readonly TauxChangeService $tauxChange,
    ) {}

    public function soldeClient(Client $client): array
    {
        $parDevise = [];
        $dzd = ['total_facture' => 0.0, 'total_paye' => 0.0];

        foreach ($this->factures($client)->get() as $facture) {
            $montant = (float) $facture->montant_total;
            $devise = $facture->devise;

            $parDevise[$devise->value]['total_facture'] = ($parDevise[$devise->value]['total_facture'] ?? 0.0) + $montant;
            $dzd['total_facture'] += $this->tauxChange->convertirEnDzd($montant, $devise, $facture->date_emission);
        }

        foreach ($client->paiements as $paiement) {
            $montant = (float) $paiement->montant;
            $devise = $paiement->devise;

            $parDevise[$devise->value]['total_paye'] = ($parDevise[$devise->value]['total_paye'] ?? 0.0) + $montant;
            $dzd['total_paye'] += $this->tauxChange->convertirEnDzd($montant, $devise, $paiement->date_paiement);
        }

        $dzd['total_facture'] = round($dzd['total_facture'], 2);
        $dzd['total_paye'] = round($dzd['total_paye'], 2);
        $dzd['reste_a_payer'] = round($dzd['total_facture'] - $dzd['total_paye'], 2);

        return ['dzd' => $dzd, 'par_devise' => $parDevise];
    }

    public function resteParFacture(DocumentCommercial $facture): float
    {
        $paye = (float) $facture->paiements()->sum('montant');

        return round((float) $facture->montant_total - $paye, 2);
    }

    private function factures(Client $client)
    {
        return $client->documentsCommerciaux()
            ->where('type', DocumentCommercialType::Facture->value)
            ->whereNotIn('statut', [
                DocumentCommercialStatut::Brouillon->value,
                DocumentCommercialStatut::Annule->value,
            ]);
    }
}

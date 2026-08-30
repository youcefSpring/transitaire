<?php

namespace App\Services;

use App\Enums\FraisSens;
use App\Models\Dossier;

class MargeService
{
    public function __construct(
        private readonly TauxChangeService $tauxChange,
    ) {}

    public function margeDossier(Dossier $dossier): array
    {
        $parDevise = [];
        $dzd = ['facture_client' => 0.0, 'supporte_transitaire' => 0.0];

        foreach ($dossier->frais as $frai) {
            $devise = $frai->devise->value;
            $montant = (float) $frai->montant;

            $parDevise[$devise] ??= ['facture_client' => 0.0, 'supporte_transitaire' => 0.0];

            if ($frai->sens === FraisSens::FactureClient) {
                $parDevise[$devise]['facture_client'] += $montant;
                $dzd['facture_client'] += $this->tauxChange->convertirEnDzd($montant, $frai->devise, $frai->date_frais);
            } else {
                $parDevise[$devise]['supporte_transitaire'] += $montant;
                $dzd['supporte_transitaire'] += $this->tauxChange->convertirEnDzd($montant, $frai->devise, $frai->date_frais);
            }
        }

        $dzd['facture_client'] = round($dzd['facture_client'], 2);
        $dzd['supporte_transitaire'] = round($dzd['supporte_transitaire'], 2);
        $dzd['marge_reelle'] = round($dzd['facture_client'] - $dzd['supporte_transitaire'], 2);

        return ['dzd' => $dzd, 'par_devise' => $parDevise];
    }
}

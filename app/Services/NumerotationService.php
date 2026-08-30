<?php

namespace App\Services;

use App\Enums\DocumentCommercialType;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use Carbon\Carbon;

class NumerotationService
{
    public const PREFIXE_DOSSIER = 'TR';

    public function prochainNumeroDossier(): string
    {
        $annee = Carbon::today()->year;

        $dernier = Dossier::withTrashed()
            ->where('numero', 'like', self::PREFIXE_DOSSIER."-{$annee}-%")
            ->max('numero');

        return sprintf('%s-%d-%04d', self::PREFIXE_DOSSIER, $annee, $this->sequenceSuivante($dernier));
    }

    public function prochainNumeroDocument(DocumentCommercialType $type): string
    {
        $prefixe = $this->prefixe($type);
        $annee = Carbon::today()->year;

        $dernier = DocumentCommercial::withTrashed()
            ->where('type', $type->value)
            ->where('numero', 'like', "{$prefixe}-{$annee}-%")
            ->max('numero');

        return sprintf('%s-%d-%04d', $prefixe, $annee, $this->sequenceSuivante($dernier));
    }

    private function prefixe(DocumentCommercialType $type): string
    {
        return match ($type) {
            DocumentCommercialType::Devis => 'DV',
            DocumentCommercialType::BonCommande => 'BC',
            DocumentCommercialType::Facture => 'FA',
            DocumentCommercialType::Avoir => 'AV',
            DocumentCommercialType::Recu => 'RC',
            DocumentCommercialType::SituationClient => 'ST',
        };
    }

    private function sequenceSuivante(?string $dernier): int
    {
        return ($dernier === null ? 0 : (int) substr($dernier, -4)) + 1;
    }
}

<?php

namespace App\Services;

use App\Enums\Devise;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use RuntimeException;

class TauxChangeService
{
    public function tauxPour(Devise $devise, ?Carbon $date = null): ?ExchangeRate
    {
        if ($devise === Devise::DZD) {
            return null;
        }

        return ExchangeRate::where('devise', $devise->value)
            ->where('date_taux', '<=', ($date ?? Carbon::today())->toDateString())
            ->orderByDesc('date_taux')
            ->first();
    }

    public function convertirEnDzd(float $montant, Devise $devise, ?Carbon $date = null): float
    {
        if ($devise === Devise::DZD) {
            return round($montant, 2);
        }

        $taux = $this->tauxPour($devise, $date);

        if ($taux === null) {
            throw new RuntimeException("Aucun taux de change {$devise->value} disponible à la date du ".($date ?? Carbon::today())->format('d/m/Y').'.');
        }

        return round($montant * (float) $taux->taux_dzd, 2);
    }
}

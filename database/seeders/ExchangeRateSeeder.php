<?php

namespace Database\Seeders;

use App\Enums\Devise;
use App\Enums\UserProfile;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Taux de change journaliers EUR/USD vers DZD (G-06 : DZD = pivot).
 */
class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $comptable = User::where('profile', UserProfile::Comptable)->firstOrFail();

        $taux = [
            Devise::EUR->value => 151.250000,
            Devise::USD->value => 134.800000,
        ];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            foreach ($taux as $devise => $base) {
                ExchangeRate::updateOrCreate(
                    ['devise' => $devise, 'date_taux' => $date],
                    [
                        'taux_dzd' => round($base + (($i % 5) * 0.35), 6),
                        'created_by' => $comptable->id,
                    ]
                );
            }
        }
    }
}

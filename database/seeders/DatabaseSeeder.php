<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            FournisseurSeeder::class,
            FlotteSeeder::class,
            ExchangeRateSeeder::class,
            DossierSeeder::class,
            DocumentCommercialSeeder::class,
            LivraisonSeeder::class,
            AlerteSeeder::class,
            NotificationSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}

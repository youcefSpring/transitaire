<?php

namespace Database\Seeders;

use App\Enums\FournisseurType;
use App\Models\Fournisseur;
use Illuminate\Database\Seeder;

/**
 * Fournisseurs et prestataires (§10).
 */
class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        $fournisseurs = [
            ['nom' => 'CMA CGM Algérie', 'type' => FournisseurType::CompagnieMaritime, 'adresse' => 'Quai nord, Port d\'Alger', 'telephone' => '+213 21 42 10 10', 'email' => 'alger@cma-cgm.dz', 'contact' => 'Service booking'],
            ['nom' => 'Air Algérie Cargo', 'type' => FournisseurType::CompagnieAerienne, 'adresse' => 'Aéroport Houari Boumediene, Dar El Beïda', 'telephone' => '+213 21 50 91 91', 'email' => 'cargo@airalgerie.dz', 'contact' => 'Comptoir fret'],
            ['nom' => 'Transport Rapide Zeroual', 'type' => FournisseurType::Transporteur, 'adresse' => 'Rouiba, Alger', 'telephone' => '+213 661 00 11 22', 'email' => 'contact@trz.dz', 'contact' => 'Mourad Zeroual'],
            ['nom' => 'EPAL Manutention', 'type' => FournisseurType::Manutention, 'adresse' => 'Port d\'Alger', 'telephone' => '+213 21 45 60 70', 'email' => 'manutention@epal.dz', 'contact' => 'Bureau exploitation'],
            ['nom' => 'Entrepôts Logistiques Rouiba', 'type' => FournisseurType::Entrepot, 'adresse' => 'Zone industrielle Rouiba, Alger', 'telephone' => '+213 21 85 33 44', 'email' => 'stock@elr.dz', 'contact' => 'Nabil Ouali'],
            ['nom' => 'Cabinet Douane Conseil', 'type' => FournisseurType::Prestataire, 'adresse' => 'Hydra, Alger', 'telephone' => '+213 23 47 88 99', 'email' => 'conseil@dc.dz', 'contact' => 'Me Bensalem'],
        ];

        foreach ($fournisseurs as $data) {
            Fournisseur::updateOrCreate(['nom' => $data['nom']], $data);
        }
    }
}

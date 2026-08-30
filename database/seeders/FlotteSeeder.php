<?php

namespace Database\Seeders;

use App\Models\Camion;
use App\Models\Chauffeur;
use Illuminate\Database\Seeder;

/**
 * Camions internes et chauffeurs (§11).
 */
class FlotteSeeder extends Seeder
{
    public function run(): void
    {
        $camions = [
            ['immatriculation' => '00123-116-16', 'notes' => 'Porte-conteneur 40 pieds'],
            ['immatriculation' => '00456-118-16', 'notes' => 'Porte-conteneur 20 pieds'],
            ['immatriculation' => '00789-117-31', 'notes' => 'Camion plateau'],
        ];

        foreach ($camions as $camion) {
            Camion::updateOrCreate(['immatriculation' => $camion['immatriculation']], $camion);
        }

        $chauffeurs = [
            ['nom' => 'Ali Ferhat', 'telephone' => '+213 661 45 78 90'],
            ['nom' => 'Kamel Bouzid', 'telephone' => '+213 770 12 45 78'],
            ['nom' => 'Toufik Slimani', 'telephone' => '+213 555 78 90 12'],
        ];

        foreach ($chauffeurs as $chauffeur) {
            Chauffeur::updateOrCreate(['nom' => $chauffeur['nom']], $chauffeur);
        }
    }
}

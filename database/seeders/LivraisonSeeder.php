<?php

namespace Database\Seeders;

use App\Enums\LivraisonStatut;
use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\Dossier;
use App\Models\Fournisseur;
use App\Models\Livraison;
use Illuminate\Database\Seeder;

/**
 * Livraisons : port/aéroport → entrepôt → client (§11).
 */
class LivraisonSeeder extends Seeder
{
    public function run(): void
    {
        $camions = Camion::orderBy('id')->get()->values();
        $chauffeurs = Chauffeur::orderBy('id')->get()->values();
        $externe = Fournisseur::where('nom', 'Transport Rapide Zeroual')->firstOrFail();

        $livraisons = [
            [
                'dossier' => 'TR-2026-0158',
                'camion_id' => $camions[0]->id,
                'transporteur_externe_id' => null,
                'chauffeur_id' => $chauffeurs[0]->id,
                'lieu_chargement' => 'Port d\'Alger',
                'entrepot' => 'Entrepôts Logistiques Rouiba',
                'destination' => 'SARL Import Maghreb — Alger Centre',
                'date_heure_chargement' => now()->subDays(14)->setTime(7, 0),
                'date_heure_livraison' => now()->subDays(14)->setTime(15, 30),
                'frais_transport' => 32000.00,
                'bon_livraison' => 'BL-2026-0231',
                'statut' => LivraisonStatut::Livree,
            ],
            [
                'dossier' => 'TR-2026-0159',
                'camion_id' => null,
                'transporteur_externe_id' => $externe->id,
                'chauffeur_id' => null,
                'lieu_chargement' => 'Aéroport Houari Boumediene',
                'entrepot' => null,
                'destination' => 'EURL Techno Distribution — Oran',
                'date_heure_chargement' => now()->subDays(1)->setTime(6, 30),
                'date_heure_livraison' => null,
                'frais_transport' => 58000.00,
                'bon_livraison' => 'BL-2026-0244',
                'statut' => LivraisonStatut::EnCours,
            ],
            [
                'dossier' => 'TR-2026-0162',
                'camion_id' => $camions[2]->id,
                'transporteur_externe_id' => null,
                'chauffeur_id' => $chauffeurs[1]->id,
                'lieu_chargement' => 'Poste frontalier de Bouchebka',
                'entrepot' => 'Entrepôts Logistiques Rouiba',
                'destination' => 'SARL Import Maghreb — Alger Centre',
                'date_heure_chargement' => now()->addDays(2)->setTime(8, 0),
                'date_heure_livraison' => null,
                'frais_transport' => 27000.00,
                'bon_livraison' => null,
                'statut' => LivraisonStatut::Planifiee,
            ],
        ];

        foreach ($livraisons as $data) {
            $dossier = Dossier::where('numero', $data['dossier'])->firstOrFail();
            unset($data['dossier']);

            Livraison::updateOrCreate(
                ['dossier_id' => $dossier->id, 'lieu_chargement' => $data['lieu_chargement']],
                $data + ['dossier_id' => $dossier->id]
            );
        }
    }
}

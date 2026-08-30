<?php

namespace Database\Seeders;

use App\Enums\Devise;
use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Enums\FraisCategorie;
use App\Enums\PaiementMode;
use App\Enums\UserProfile;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Devis, factures et paiements (§8/§9).
 */
class DocumentCommercialSeeder extends Seeder
{
    public function run(): void
    {
        $commercial = User::where('profile', UserProfile::AgentCommercial)->firstOrFail();
        $comptable = User::where('profile', UserProfile::Comptable)->firstOrFail();

        $documents = [
            [
                'type' => DocumentCommercialType::Devis,
                'numero' => 'DV-2026-0042',
                'dossier' => 'TR-2026-0160',
                'statut' => DocumentCommercialStatut::Envoye,
                'date_emission' => now()->subDays(9)->toDateString(),
                'date_echeance' => now()->addDays(21)->toDateString(),
                'total_taxes' => 0,
                'remise' => 0,
                'lignes' => [
                    ['designation' => 'Honoraires de transit', 'categorie' => FraisCategorie::Transit, 'quantite' => 1, 'prix_unitaire' => 52000.00],
                    ['designation' => 'Dédouanement import', 'categorie' => FraisCategorie::Dedouanement, 'quantite' => 1, 'prix_unitaire' => 31000.00],
                ],
                'paiements' => [],
            ],
            [
                'type' => DocumentCommercialType::Facture,
                'numero' => 'FA-2026-0117',
                'dossier' => 'TR-2026-0158',
                'statut' => DocumentCommercialStatut::Paye,
                'date_emission' => now()->subDays(16)->toDateString(),
                'date_echeance' => now()->subDays(1)->toDateString(),
                'total_taxes' => 20710.00,
                'remise' => 5000.00,
                'lignes' => [
                    ['designation' => 'Honoraires de transit', 'categorie' => FraisCategorie::Transit, 'quantite' => 1, 'prix_unitaire' => 45000.00],
                    ['designation' => 'Frais de dédouanement', 'categorie' => FraisCategorie::Dedouanement, 'quantite' => 1, 'prix_unitaire' => 28000.00],
                    ['designation' => 'Manutention portuaire', 'categorie' => FraisCategorie::Manutention, 'quantite' => 2, 'prix_unitaire' => 18000.00],
                ],
                'paiements' => [
                    ['mode' => PaiementMode::Virement, 'montant' => 122710.00, 'jours' => 3, 'reference' => 'VIR-BNA-778412'],
                ],
            ],
            [
                'type' => DocumentCommercialType::Facture,
                'numero' => 'FA-2026-0118',
                'dossier' => 'TR-2026-0159',
                'statut' => DocumentCommercialStatut::PartiellementPaye,
                'date_emission' => now()->subDays(5)->toDateString(),
                'date_echeance' => now()->addDays(25)->toDateString(),
                'total_taxes' => 15390.00,
                'remise' => 0,
                'lignes' => [
                    ['designation' => 'Honoraires de transit aérien', 'categorie' => FraisCategorie::Transit, 'quantite' => 1, 'prix_unitaire' => 38000.00],
                    ['designation' => 'Frais administratifs', 'categorie' => FraisCategorie::FraisAdministratifs, 'quantite' => 1, 'prix_unitaire' => 12000.00],
                    ['designation' => 'Stockage sous douane (5 jours)', 'categorie' => FraisCategorie::Stockage, 'quantite' => 5, 'prix_unitaire' => 3000.00],
                ],
                'paiements' => [
                    ['mode' => PaiementMode::Cheque, 'montant' => 40000.00, 'jours' => 2, 'reference' => 'CHQ-4471203'],
                ],
            ],
            [
                'type' => DocumentCommercialType::Facture,
                'numero' => 'FA-2026-0119',
                'dossier' => 'TR-2026-0161',
                'statut' => DocumentCommercialStatut::Emis,
                'date_emission' => now()->subDays(40)->toDateString(),
                'date_echeance' => now()->subDays(10)->toDateString(),
                'total_taxes' => 13300.00,
                'remise' => 0,
                'lignes' => [
                    ['designation' => 'Honoraires de transit export', 'categorie' => FraisCategorie::Transit, 'quantite' => 1, 'prix_unitaire' => 41000.00],
                    ['designation' => 'Frais portuaires', 'categorie' => FraisCategorie::FraisPortuaires, 'quantite' => 1, 'prix_unitaire' => 29000.00],
                ],
                'paiements' => [],
            ],
            [
                'type' => DocumentCommercialType::SituationClient,
                'numero' => 'SC-2026-0009',
                'dossier' => null,
                'client_numero' => 'TR-2026-0158',
                'statut' => DocumentCommercialStatut::Emis,
                'date_emission' => now()->toDateString(),
                'date_echeance' => null,
                'total_taxes' => 0,
                'remise' => 0,
                'lignes' => [],
                'paiements' => [
                    ['mode' => PaiementMode::Versement, 'montant' => 100000.00, 'jours' => 0, 'reference' => 'VRS-CPA-9981'],
                ],
            ],
        ];

        foreach ($documents as $data) {
            $reference = $data['dossier'] ?? $data['client_numero'];
            $dossier = Dossier::where('numero', $reference)->firstOrFail();
            $lignes = $data['lignes'];
            $paiements = $data['paiements'];

            $totalPrestations = array_sum(array_map(
                fn (array $ligne) => $ligne['quantite'] * $ligne['prix_unitaire'],
                $lignes
            ));
            $totalFrais = (float) $dossier->frais()->where('sens', \App\Enums\FraisSens::FactureClient)->sum('montant');
            $montantTotal = $totalPrestations + $totalFrais + $data['total_taxes'] - $data['remise'];

            $document = DocumentCommercial::updateOrCreate(
                ['numero' => $data['numero']],
                [
                    'type' => $data['type'],
                    'client_id' => $dossier->client_id,
                    'dossier_id' => $data['dossier'] ? $dossier->id : null,
                    'devise' => Devise::DZD,
                    'total_prestations' => $totalPrestations,
                    'total_frais' => $totalFrais,
                    'total_taxes' => $data['total_taxes'],
                    'remise' => $data['remise'],
                    'montant_total' => $montantTotal,
                    'statut' => $data['statut'],
                    'date_emission' => $data['date_emission'],
                    'date_echeance' => $data['date_echeance'],
                    'created_by' => $commercial->id,
                ]
            );

            foreach ($lignes as $ligne) {
                $document->lignes()->updateOrCreate(
                    ['designation' => $ligne['designation']],
                    $ligne + ['montant' => $ligne['quantite'] * $ligne['prix_unitaire']]
                );
            }

            foreach ($paiements as $paiement) {
                $document->paiements()->updateOrCreate(
                    ['reference' => $paiement['reference']],
                    [
                        'client_id' => $document->client_id,
                        'mode' => $paiement['mode'],
                        'montant' => $paiement['montant'],
                        'devise' => Devise::DZD,
                        'date_paiement' => now()->subDays($paiement['jours'])->toDateString(),
                        'created_by' => $comptable->id,
                    ]
                );
            }
        }
    }
}

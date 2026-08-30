<?php

namespace Database\Seeders;

use App\Enums\UserProfile;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Clients et leurs contacts (§1).
 */
class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $commercial = User::where('profile', UserProfile::AgentCommercial)->firstOrFail();

        $clients = [
            [
                'raison_sociale' => 'SARL Import Maghreb',
                'nif' => '000216001234567',
                'nis' => '000216009876543',
                'rc' => '16/00-1234567B16',
                'adresse' => '12 rue Didouche Mourad, Alger Centre, Alger',
                'telephone' => '+213 21 63 45 12',
                'email' => 'contact@import-maghreb.dz',
                'conditions_paiement' => '30 jours fin de mois',
                'contacts' => [
                    ['nom' => 'Amine Boudjelal', 'fonction' => 'Directeur achats', 'telephone' => '+213 661 12 34 56', 'email' => 'a.boudjelal@import-maghreb.dz'],
                    ['nom' => 'Lynda Saïdi', 'fonction' => 'Comptabilité', 'telephone' => '+213 770 98 76 54', 'email' => 'compta@import-maghreb.dz'],
                ],
            ],
            [
                'raison_sociale' => 'EURL Techno Distribution',
                'nif' => '000231002345678',
                'nis' => '000231008765432',
                'rc' => '31/00-2345678B31',
                'adresse' => 'Zone industrielle Es Sénia, Oran',
                'telephone' => '+213 41 58 22 33',
                'email' => 'info@techno-dist.dz',
                'conditions_paiement' => 'Paiement à réception',
                'contacts' => [
                    ['nom' => 'Farid Benali', 'fonction' => 'Gérant', 'telephone' => '+213 550 44 33 22', 'email' => 'f.benali@techno-dist.dz'],
                ],
            ],
            [
                'raison_sociale' => 'SPA Agro Industrie Est',
                'nif' => '000225003456789',
                'nis' => '000225007654321',
                'rc' => '25/00-3456789B25',
                'adresse' => 'Route de Skikda, Constantine',
                'telephone' => '+213 31 92 11 44',
                'email' => 'direction@agro-est.dz',
                'conditions_paiement' => '45 jours date de facture',
                'contacts' => [
                    ['nom' => 'Samir Lounis', 'fonction' => 'Responsable logistique', 'telephone' => '+213 662 77 88 99', 'email' => 's.lounis@agro-est.dz'],
                    ['nom' => 'Hocine Merabet', 'fonction' => 'Magasinier', 'telephone' => '+213 555 10 20 30', 'email' => null],
                ],
            ],
            [
                'raison_sociale' => 'SARL Bâtimat Export',
                'nif' => '000209004567890',
                'nis' => '000209006543210',
                'rc' => '09/00-4567890B09',
                'adresse' => 'Cité des Frères Bouchama, Blida',
                'telephone' => '+213 25 41 77 88',
                'email' => 'export@batimat.dz',
                'conditions_paiement' => '60 jours',
                'contacts' => [
                    ['nom' => 'Djamel Ait Ali', 'fonction' => 'Responsable export', 'telephone' => '+213 668 55 44 33', 'email' => 'd.aitali@batimat.dz'],
                ],
            ],
        ];

        foreach ($clients as $data) {
            $contacts = $data['contacts'];
            unset($data['contacts']);

            $client = Client::updateOrCreate(
                ['nif' => $data['nif']],
                $data + ['created_by' => $commercial->id]
            );

            foreach ($contacts as $contact) {
                $client->contacts()->updateOrCreate(
                    ['nom' => $contact['nom']],
                    $contact
                );
            }
        }
    }
}

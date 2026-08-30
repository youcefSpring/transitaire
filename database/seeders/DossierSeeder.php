<?php

namespace Database\Seeders;

use App\Enums\ConteneurStatut;
use App\Enums\Devise;
use App\Enums\DocumentCategorie;
use App\Enums\DossierStatut;
use App\Enums\DouaneEtape;
use App\Enums\FraisCategorie;
use App\Enums\FraisSens;
use App\Enums\ModeTransport;
use App\Enums\TypeOperation;
use App\Enums\UserProfile;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Dossiers de transit et leurs dépendances : marchandises (§3), conteneurs (§4),
 * documents (§5), étapes de dédouanement (§6) et frais (§7).
 */
class DossierSeeder extends Seeder
{
    public function run(): void
    {
        $agent = User::where('profile', UserProfile::AgentTransit)->firstOrFail();
        $clients = Client::orderBy('id')->get()->values();
        $maritime = Fournisseur::where('nom', 'CMA CGM Algérie')->firstOrFail();
        $manutention = Fournisseur::where('nom', 'EPAL Manutention')->firstOrFail();
        $transporteur = Fournisseur::where('nom', 'Transport Rapide Zeroual')->firstOrFail();

        $dossiers = [
            [
                'numero' => 'TR-2026-0158',
                'client' => 0,
                'type' => TypeOperation::Import,
                'mode_transport' => ModeTransport::Maritime,
                'port_aeroport' => 'Port d\'Alger',
                'fournisseur_destinataire' => 'Shanghai Metal Works Co. Ltd',
                'date_arrivee_prevue' => now()->subDays(20)->toDateString(),
                'date_arrivee_reelle' => now()->subDays(18)->toDateString(),
                'numero_bl_awb' => 'CMDU7714520',
                'nombre_colis' => 420,
                'poids' => 18450.500,
                'volume' => 54.200,
                'nature_marchandise' => 'Profilés en acier galvanisé',
                'valeur_declaree' => 96500.00,
                'devise' => Devise::USD,
                'incoterm' => 'CFR',
                'statut' => DossierStatut::Cloture,
                'etapes' => [DouaneEtape::Declaration, DouaneEtape::Depot, DouaneEtape::ControleDocumentaire, DouaneEtape::Liquidation, DouaneEtape::Paiement, DouaneEtape::Mainlevee, DouaneEtape::Sortie],
                'marchandises' => [
                    ['designation' => 'Profilé IPE 200', 'quantite' => 240.000, 'unite' => 'barre', 'nombre_colis' => 240, 'poids' => 11040.000, 'volume' => 32.000, 'valeur' => 58000.00, 'pays_origine' => 'Chine', 'code_tarifaire' => '7216.32.00'],
                    ['designation' => 'Tôle galvanisée 2 mm', 'quantite' => 180.000, 'unite' => 'plaque', 'nombre_colis' => 180, 'poids' => 7410.500, 'volume' => 22.200, 'valeur' => 38500.00, 'pays_origine' => 'Chine', 'code_tarifaire' => '7210.49.00'],
                ],
                'conteneurs' => [
                    ['numero' => 'CMAU1234567', 'type' => '40 HC', 'navire' => 'CMA CGM Tanger', 'statut' => ConteneurStatut::Retourne],
                    ['numero' => 'CMAU7654321', 'type' => '20 DV', 'navire' => 'CMA CGM Tanger', 'statut' => ConteneurStatut::Retourne],
                ],
            ],
            [
                'numero' => 'TR-2026-0159',
                'client' => 1,
                'type' => TypeOperation::Import,
                'mode_transport' => ModeTransport::Aerien,
                'port_aeroport' => 'Aéroport Houari Boumediene',
                'fournisseur_destinataire' => 'Elektro Handel GmbH',
                'date_arrivee_prevue' => now()->subDays(6)->toDateString(),
                'date_arrivee_reelle' => now()->subDays(6)->toDateString(),
                'numero_bl_awb' => '124-88451236',
                'nombre_colis' => 32,
                'poids' => 860.000,
                'volume' => 4.800,
                'nature_marchandise' => 'Composants électroniques',
                'valeur_declaree' => 47200.00,
                'devise' => Devise::EUR,
                'incoterm' => 'CIP',
                'statut' => DossierStatut::DouaneTerminee,
                'etapes' => [DouaneEtape::Declaration, DouaneEtape::Depot, DouaneEtape::ControleDocumentaire, DouaneEtape::Visite, DouaneEtape::Liquidation, DouaneEtape::Paiement, DouaneEtape::Mainlevee],
                'marchandises' => [
                    ['designation' => 'Cartes mères industrielles', 'quantite' => 400.000, 'unite' => 'pièce', 'nombre_colis' => 20, 'poids' => 520.000, 'volume' => 3.000, 'valeur' => 32000.00, 'pays_origine' => 'Allemagne', 'code_tarifaire' => '8473.30.20'],
                    ['designation' => 'Modules d\'alimentation 24V', 'quantite' => 250.000, 'unite' => 'pièce', 'nombre_colis' => 12, 'poids' => 340.000, 'volume' => 1.800, 'valeur' => 15200.00, 'pays_origine' => 'Allemagne', 'code_tarifaire' => '8504.40.90'],
                ],
                'conteneurs' => [],
            ],
            [
                'numero' => 'TR-2026-0160',
                'client' => 2,
                'type' => TypeOperation::Import,
                'mode_transport' => ModeTransport::Maritime,
                'port_aeroport' => 'Port de Skikda',
                'fournisseur_destinataire' => 'Brasil Agro Trading SA',
                'date_arrivee_prevue' => now()->addDays(4)->toDateString(),
                'date_arrivee_reelle' => null,
                'numero_bl_awb' => 'MSCU9981234',
                'nombre_colis' => 1200,
                'poids' => 24000.000,
                'volume' => 68.000,
                'nature_marchandise' => 'Sucre roux en sacs',
                'valeur_declaree' => 18600000.00,
                'devise' => Devise::DZD,
                'incoterm' => 'FOB',
                'statut' => DossierStatut::EnCours,
                'etapes' => [DouaneEtape::Declaration],
                'marchandises' => [
                    ['designation' => 'Sucre roux sacs 50 kg', 'quantite' => 1200.000, 'unite' => 'sac', 'nombre_colis' => 1200, 'poids' => 24000.000, 'volume' => 68.000, 'valeur' => 18600000.00, 'pays_origine' => 'Brésil', 'code_tarifaire' => '1701.14.90'],
                ],
                'conteneurs' => [
                    ['numero' => 'MSCU4455661', 'type' => '40 DV', 'navire' => 'MSC Livorno', 'statut' => ConteneurStatut::EnAttente],
                ],
            ],
            [
                'numero' => 'TR-2026-0161',
                'client' => 3,
                'type' => TypeOperation::Export,
                'mode_transport' => ModeTransport::Maritime,
                'port_aeroport' => 'Port d\'Alger',
                'fournisseur_destinataire' => 'Sahel Construction SARL, Dakar',
                'date_arrivee_prevue' => now()->addDays(12)->toDateString(),
                'date_arrivee_reelle' => null,
                'numero_bl_awb' => 'CMDU8820477',
                'nombre_colis' => 640,
                'poids' => 21000.000,
                'volume' => 60.000,
                'nature_marchandise' => 'Carrelage et sanitaires',
                'valeur_declaree' => 74000.00,
                'devise' => Devise::EUR,
                'incoterm' => 'FOB',
                'statut' => DossierStatut::DocumentsRecus,
                'bloque' => true,
                'raison_blocage' => 'Certificat d\'origine en attente de visa de la chambre de commerce.',
                'etapes' => [],
                'marchandises' => [
                    ['designation' => 'Carrelage grès 60x60', 'quantite' => 480.000, 'unite' => 'palette', 'nombre_colis' => 480, 'poids' => 16800.000, 'volume' => 44.000, 'valeur' => 52000.00, 'pays_origine' => 'Algérie', 'code_tarifaire' => '6907.21.00'],
                    ['designation' => 'Sanitaires céramique', 'quantite' => 160.000, 'unite' => 'colis', 'nombre_colis' => 160, 'poids' => 4200.000, 'volume' => 16.000, 'valeur' => 22000.00, 'pays_origine' => 'Algérie', 'code_tarifaire' => '6910.10.00'],
                ],
                'conteneurs' => [
                    ['numero' => 'CMAU3311220', 'type' => '40 HC', 'navire' => 'CMA CGM Oran', 'statut' => ConteneurStatut::Sorti],
                ],
            ],
            [
                'numero' => 'TR-2026-0162',
                'client' => 0,
                'type' => TypeOperation::Import,
                'mode_transport' => ModeTransport::Terrestre,
                'port_aeroport' => 'Poste frontalier de Bouchebka',
                'fournisseur_destinataire' => 'Tunis Plast SARL',
                'date_arrivee_prevue' => now()->subDays(2)->toDateString(),
                'date_arrivee_reelle' => now()->subDays(1)->toDateString(),
                'numero_bl_awb' => 'CMR-2026-00871',
                'nombre_colis' => 90,
                'poids' => 6400.000,
                'volume' => 28.000,
                'nature_marchandise' => 'Granulés plastiques',
                'valeur_declaree' => 29500.00,
                'devise' => Devise::EUR,
                'incoterm' => 'DAP',
                'statut' => DossierStatut::Dedouanement,
                'etapes' => [DouaneEtape::Declaration, DouaneEtape::Depot, DouaneEtape::ControleDocumentaire],
                'marchandises' => [
                    ['designation' => 'Granulés PEHD sacs 25 kg', 'quantite' => 90.000, 'unite' => 'big bag', 'nombre_colis' => 90, 'poids' => 6400.000, 'volume' => 28.000, 'valeur' => 29500.00, 'pays_origine' => 'Tunisie', 'code_tarifaire' => '3901.20.00'],
                ],
                'conteneurs' => [],
            ],
        ];

        foreach ($dossiers as $data) {
            $client = $clients[$data['client']];
            $marchandises = $data['marchandises'];
            $conteneurs = $data['conteneurs'];
            $etapes = $data['etapes'];
            unset($data['client'], $data['marchandises'], $data['conteneurs'], $data['etapes']);

            $dossier = Dossier::updateOrCreate(
                ['numero' => $data['numero']],
                $data + [
                    'client_id' => $client->id,
                    'created_by' => $agent->id,
                    'bloque' => false,
                    'raison_blocage' => null,
                ]
            );

            foreach ($marchandises as $marchandise) {
                $dossier->marchandises()->updateOrCreate(
                    ['designation' => $marchandise['designation']],
                    $marchandise
                );
            }

            foreach ($conteneurs as $conteneur) {
                $dossier->conteneurs()->updateOrCreate(
                    ['numero' => $conteneur['numero']],
                    $conteneur + [
                        'numero_bl' => $dossier->numero_bl_awb,
                        'port_depart' => $dossier->type === TypeOperation::Export ? $dossier->port_aeroport : 'Shanghai',
                        'port_arrivee' => $dossier->type === TypeOperation::Export ? 'Dakar' : $dossier->port_aeroport,
                        'date_eta' => $dossier->date_arrivee_prevue,
                        'date_ata' => $dossier->date_arrivee_reelle,
                        'client_id' => $client->id,
                        'date_sortie' => $conteneur['statut'] === ConteneurStatut::EnAttente ? null : now()->subDays(10)->toDateString(),
                        'date_retour' => $conteneur['statut'] === ConteneurStatut::Retourne ? now()->subDays(5)->toDateString() : null,
                    ]
                );
            }

            $jour = count($etapes) + 1;
            foreach ($etapes as $etape) {
                $dossier->douaneEtapes()->updateOrCreate(
                    ['etape' => $etape],
                    [
                        'executed_by' => $agent->id,
                        'executed_at' => now()->subDays($jour--)->setTime(9, 30),
                        'notes' => null,
                    ]
                );
            }

            $this->seedDocuments($dossier, $agent->id);
            $this->seedFrais($dossier, $agent->id, $maritime->id, $manutention->id, $transporteur->id);
        }
    }

    /**
     * Documents rattachés au dossier (§5, relation polymorphe).
     */
    private function seedDocuments(Dossier $dossier, int $userId): void
    {
        $documents = [
            [DocumentCategorie::FactureCommerciale, 'facture_commerciale.pdf', 'application/pdf', 184320],
            [DocumentCategorie::PackingList, 'packing_list.pdf', 'application/pdf', 96256],
            [DocumentCategorie::BillOfLading, 'bill_of_lading.pdf', 'application/pdf', 122880],
        ];

        foreach ($documents as [$categorie, $nom, $mime, $taille]) {
            $dossier->documents()->updateOrCreate(
                ['categorie' => $categorie],
                [
                    'nom_original' => $nom,
                    'chemin' => "documents/dossiers/{$dossier->numero}/{$nom}",
                    'mime_type' => $mime,
                    'taille' => $taille,
                    'televerse_par' => $userId,
                ]
            );
        }
    }

    /**
     * Frais facturés au client et supportés par le transitaire (§7).
     */
    private function seedFrais(Dossier $dossier, int $userId, int $maritimeId, int $manutentionId, int $transporteurId): void
    {
        $frais = [
            [FraisSens::FactureClient, FraisCategorie::Transit, 'Honoraires de transit', 45000.00, null],
            [FraisSens::FactureClient, FraisCategorie::Dedouanement, 'Frais de dédouanement', 28000.00, null],
            [FraisSens::FactureClient, FraisCategorie::Manutention, 'Manutention portuaire', 36000.00, null],
            [FraisSens::SupporteTransitaire, FraisCategorie::Port, 'Redevances portuaires', 21500.00, $maritimeId],
            [FraisSens::SupporteTransitaire, FraisCategorie::Manutention, 'Prestation de manutention', 18000.00, $manutentionId],
            [FraisSens::SupporteTransitaire, FraisCategorie::Transporteur, 'Acheminement port → entrepôt', 32000.00, $transporteurId],
        ];

        foreach ($frais as [$sens, $categorie, $libelle, $montant, $fournisseurId]) {
            $dossier->frais()->updateOrCreate(
                ['sens' => $sens, 'categorie' => $categorie],
                [
                    'libelle' => $libelle,
                    'montant' => $montant,
                    'devise' => Devise::DZD,
                    'fournisseur_id' => $fournisseurId,
                    'date_frais' => now()->subDays(7)->toDateString(),
                    'created_by' => $userId,
                ]
            );
        }
    }
}

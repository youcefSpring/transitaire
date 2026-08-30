<?php

namespace Database\Seeders;

use App\Enums\AlerteStatut;
use App\Enums\AlerteType;
use App\Models\Alerte;
use App\Models\Dossier;
use Illuminate\Database\Seeder;

/**
 * Alertes opérationnelles (§12).
 */
class AlerteSeeder extends Seeder
{
    public function run(): void
    {
        $alertes = [
            ['TR-2026-0160', AlerteType::ArriveeNavire, 'Arrivée prévue du navire MSC Livorno dans 4 jours (dossier TR-2026-0160).', 4, AlerteStatut::Nouvelle],
            ['TR-2026-0161', AlerteType::DossierBloque, 'Dossier TR-2026-0161 bloqué : certificat d\'origine en attente de visa.', 0, AlerteStatut::Nouvelle],
            ['TR-2026-0161', AlerteType::FactureImpayee, 'Facture FA-2026-0119 échue depuis 10 jours.', -10, AlerteStatut::Lue],
            ['TR-2026-0162', AlerteType::LivraisonAEffectuer, 'Livraison planifiée dans 2 jours pour le dossier TR-2026-0162.', 2, AlerteStatut::Nouvelle],
            ['TR-2026-0158', AlerteType::ConteneurARetourner, 'Conteneurs CMAU1234567 et CMAU7654321 restitués — surestaries évitées.', -5, AlerteStatut::Traitee],
            ['TR-2026-0159', AlerteType::DocumentManquant, 'Certificat de conformité manquant pour le dossier TR-2026-0159.', 3, AlerteStatut::Nouvelle],
        ];

        foreach ($alertes as [$numero, $type, $message, $jours, $statut]) {
            $dossier = Dossier::where('numero', $numero)->firstOrFail();

            Alerte::updateOrCreate(
                ['type' => $type, 'dossier_id' => $dossier->id],
                [
                    'message' => $message,
                    'date_echeance' => now()->addDays($jours)->toDateString(),
                    'statut' => $statut,
                ]
            );
        }
    }
}

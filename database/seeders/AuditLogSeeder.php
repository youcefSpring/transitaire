<?php

namespace Database\Seeders;

use App\Enums\UserProfile;
use App\Models\AuditLog;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Journal d\'audit append-only (§15).
 */
class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $agent = User::where('profile', UserProfile::AgentTransit)->firstOrFail();
        $comptable = User::where('profile', UserProfile::Comptable)->firstOrFail();

        $entrees = [
            [$agent, 'TR-2026-0158', 'Clôture du dossier #TR-2026-0158', 12],
            [$agent, 'TR-2026-0159', 'Enregistrement de la mainlevée du dossier #TR-2026-0159', 2],
            [$comptable, 'TR-2026-0158', 'Encaissement du virement VIR-BNA-778412 (122 710,00 DZD)', 3],
            [$agent, 'TR-2026-0161', 'Blocage du dossier #TR-2026-0161 — certificat d\'origine manquant', 6],
            [$agent, 'TR-2026-0160', 'Création du dossier #TR-2026-0160', 9],
        ];

        foreach ($entrees as [$user, $numero, $action, $jours]) {
            $dossier = Dossier::where('numero', $numero)->firstOrFail();

            AuditLog::firstOrCreate(
                ['user_id' => $user->id, 'dossier_id' => $dossier->id, 'action' => $action],
                ['created_at' => now()->subDays($jours)->setTime(11, 0)]
            );
        }
    }
}

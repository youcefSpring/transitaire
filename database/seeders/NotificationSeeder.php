<?php

namespace Database\Seeders;

use App\Enums\NotificationCanal;
use App\Enums\NotificationStatut;
use App\Models\Client;
use App\Models\Notification;
use Illuminate\Database\Seeder;

/**
 * Notifications sortantes vers les clients (§17).
 */
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::orderBy('id')->get()->values();

        $notifications = [
            [
                'canal' => NotificationCanal::Email,
                'client' => 0,
                'sujet' => 'Dossier TR-2026-0158 clôturé',
                'message' => 'Votre dossier TR-2026-0158 est clôturé. La marchandise a été livrée et les conteneurs restitués.',
                'statut' => NotificationStatut::Envoyee,
                'jours' => 12,
            ],
            [
                'canal' => NotificationCanal::Email,
                'client' => 1,
                'sujet' => 'Mainlevée obtenue — TR-2026-0159',
                'message' => 'La mainlevée douanière a été obtenue. L\'enlèvement est programmé pour demain matin.',
                'statut' => NotificationStatut::Envoyee,
                'jours' => 2,
            ],
            [
                'canal' => NotificationCanal::Sms,
                'client' => 2,
                'sujet' => null,
                'message' => 'Navire MSC Livorno attendu au port de Skikda dans 4 jours. Dossier TR-2026-0160.',
                'statut' => NotificationStatut::EnFile,
                'jours' => null,
            ],
            [
                'canal' => NotificationCanal::Whatsapp,
                'client' => 3,
                'sujet' => null,
                'message' => 'Dossier TR-2026-0161 bloqué : merci de nous transmettre le certificat d\'origine visé.',
                'statut' => NotificationStatut::Echec,
                'jours' => 1,
            ],
        ];

        foreach ($notifications as $data) {
            $client = $clients[$data['client']];

            Notification::updateOrCreate(
                ['canal' => $data['canal'], 'client_id' => $client->id, 'message' => $data['message']],
                [
                    'destinataire' => $data['canal'] === NotificationCanal::Email ? $client->email : $client->telephone,
                    'sujet' => $data['sujet'],
                    'statut' => $data['statut'],
                    'envoyee_le' => $data['jours'] === null ? null : now()->subDays($data['jours'])->setTime(10, 15),
                    'created_at' => now()->subDays($data['jours'] ?? 0)->setTime(10, 0),
                ]
            );
        }
    }
}

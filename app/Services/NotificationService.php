<?php

namespace App\Services;

use App\Enums\NotificationCanal;
use App\Enums\NotificationStatut;
use App\Models\Client;
use App\Models\Notification;

/**
 * File des notifications clients (§17) — les messages partent en_file
 * puis sont expédiés par le canal indiqué (l'envoi effectif reste à brancher).
 */
class NotificationService
{
    public function mettreEnFile(Client $client, string $sujet, string $message): ?Notification
    {
        if ($client->email === null || $client->email === '') {
            return null;
        }

        return Notification::create([
            'canal' => NotificationCanal::Email,
            'destinataire' => $client->email,
            'client_id' => $client->id,
            'sujet' => $sujet,
            'message' => $message,
            'statut' => NotificationStatut::EnFile,
        ]);
    }
}

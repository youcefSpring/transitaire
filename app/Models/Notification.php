<?php

namespace App\Models;

use App\Enums\NotificationCanal;
use App\Enums\NotificationStatut;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['canal', 'destinataire', 'client_id', 'sujet', 'message', 'statut', 'envoyee_le', 'created_at'])]
class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'canal' => NotificationCanal::class,
            'statut' => NotificationStatut::class,
            'envoyee_le' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

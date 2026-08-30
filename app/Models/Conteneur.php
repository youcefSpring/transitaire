<?php

namespace App\Models;

use App\Enums\ConteneurStatut;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['numero', 'type', 'numero_bl', 'navire', 'port_depart', 'port_arrivee', 'date_eta', 'date_ata', 'client_id', 'dossier_id', 'statut', 'date_sortie', 'date_retour'])]
class Conteneur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conteneurs';

    protected function casts(): array
    {
        return [
            'statut' => ConteneurStatut::class,
            'date_eta' => 'date',
            'date_ata' => 'date',
            'date_sortie' => 'date',
            'date_retour' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }
}

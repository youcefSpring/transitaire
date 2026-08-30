<?php

namespace App\Models;

use App\Enums\LivraisonStatut;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['dossier_id', 'camion_id', 'transporteur_externe_id', 'chauffeur_id', 'lieu_chargement', 'entrepot', 'destination', 'date_heure_chargement', 'date_heure_livraison', 'frais_transport', 'bon_livraison', 'statut'])]
class Livraison extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'livraisons';

    protected function casts(): array
    {
        return [
            'statut' => LivraisonStatut::class,
            'date_heure_chargement' => 'datetime',
            'date_heure_livraison' => 'datetime',
            'frais_transport' => 'decimal:2',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class);
    }

    public function chauffeur(): BelongsTo
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function transporteurExterne(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'transporteur_externe_id');
    }
}

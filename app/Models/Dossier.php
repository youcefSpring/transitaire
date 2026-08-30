<?php

namespace App\Models;

use App\Enums\Devise;
use App\Enums\DossierStatut;
use App\Enums\ModeTransport;
use App\Enums\TypeOperation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['numero', 'client_id', 'type', 'mode_transport', 'port_aeroport', 'fournisseur_destinataire', 'date_arrivee_prevue', 'date_arrivee_reelle', 'numero_bl_awb', 'nombre_colis', 'poids', 'volume', 'nature_marchandise', 'valeur_declaree', 'devise', 'incoterm', 'statut', 'bloque', 'raison_blocage', 'created_by'])]
class Dossier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dossiers';

    protected function casts(): array
    {
        return [
            'type' => TypeOperation::class,
            'mode_transport' => ModeTransport::class,
            'devise' => Devise::class,
            'statut' => DossierStatut::class,
            'date_arrivee_prevue' => 'date',
            'date_arrivee_reelle' => 'date',
            'nombre_colis' => 'integer',
            'poids' => 'decimal:3',
            'volume' => 'decimal:3',
            'valeur_declaree' => 'decimal:2',
            'bloque' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function marchandises(): HasMany
    {
        return $this->hasMany(Marchandise::class);
    }

    public function conteneurs(): HasMany
    {
        return $this->hasMany(Conteneur::class);
    }

    public function douaneEtapes(): HasMany
    {
        return $this->hasMany(DedouanementEtape::class);
    }

    public function frais(): HasMany
    {
        return $this->hasMany(Frai::class);
    }

    public function documentsCommerciaux(): HasMany
    {
        return $this->hasMany(DocumentCommercial::class);
    }

    public function livraisons(): HasMany
    {
        return $this->hasMany(Livraison::class);
    }

    public function alertes(): HasMany
    {
        return $this->hasMany(Alerte::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}

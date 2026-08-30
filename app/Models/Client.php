<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['raison_sociale', 'nif', 'nis', 'rc', 'adresse', 'telephone', 'email', 'conditions_paiement', 'created_by'])]
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function conteneurs(): HasMany
    {
        return $this->hasMany(Conteneur::class);
    }

    public function documentsCommerciaux(): HasMany
    {
        return $this->hasMany(DocumentCommercial::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use App\Enums\Devise;
use App\Enums\FraisCategorie;
use App\Enums\FraisSens;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['dossier_id', 'sens', 'categorie', 'libelle', 'montant', 'devise', 'fournisseur_id', 'date_frais', 'created_by'])]
class Frai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'frais';

    protected function casts(): array
    {
        return [
            'sens' => FraisSens::class,
            'categorie' => FraisCategorie::class,
            'devise' => Devise::class,
            'montant' => 'decimal:2',
            'date_frais' => 'date',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use App\Enums\FournisseurType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nom', 'type', 'adresse', 'telephone', 'email', 'contact'])]
class Fournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fournisseurs';

    protected function casts(): array
    {
        return [
            'type' => FournisseurType::class,
        ];
    }

    public function frais(): HasMany
    {
        return $this->hasMany(Frai::class);
    }

    public function livraisonsExternes(): HasMany
    {
        return $this->hasMany(Livraison::class, 'transporteur_externe_id');
    }
}

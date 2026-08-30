<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['immatriculation', 'notes'])]
class Camion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'camions';

    public function livraisons(): HasMany
    {
        return $this->hasMany(Livraison::class);
    }
}

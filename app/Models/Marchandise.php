<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['dossier_id', 'designation', 'quantite', 'unite', 'nombre_colis', 'poids', 'volume', 'valeur', 'pays_origine', 'code_tarifaire', 'infos_complementaires'])]
class Marchandise extends Model
{
    use HasFactory;

    protected $table = 'marchandises';

    protected function casts(): array
    {
        return [
            'quantite' => 'decimal:3',
            'nombre_colis' => 'integer',
            'poids' => 'decimal:3',
            'volume' => 'decimal:3',
            'valeur' => 'decimal:2',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }
}

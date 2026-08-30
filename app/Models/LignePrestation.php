<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['document_id', 'designation', 'categorie', 'quantite', 'prix_unitaire', 'montant'])]
class LignePrestation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'lignes_prestations';

    protected function casts(): array
    {
        return [
            'quantite' => 'decimal:3',
            'prix_unitaire' => 'decimal:2',
            'montant' => 'decimal:2',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DocumentCommercial::class, 'document_id');
    }
}

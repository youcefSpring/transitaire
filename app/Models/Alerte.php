<?php

namespace App\Models;

use App\Enums\AlerteStatut;
use App\Enums\AlerteType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'message', 'dossier_id', 'ref_type', 'ref_id', 'date_echeance', 'statut'])]
class Alerte extends Model
{
    use HasFactory;

    protected $table = 'alertes';

    protected function casts(): array
    {
        return [
            'type' => AlerteType::class,
            'statut' => AlerteStatut::class,
            'ref_id' => 'integer',
            'date_echeance' => 'date',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }
}

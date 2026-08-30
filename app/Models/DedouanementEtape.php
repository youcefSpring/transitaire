<?php

namespace App\Models;

use App\Enums\DouaneEtape;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['dossier_id', 'etape', 'executed_by', 'executed_at', 'notes'])]
class DedouanementEtape extends Model
{
    use HasFactory;

    protected $table = 'dedouanement_etapes';

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'etape' => DouaneEtape::class,
            'executed_at' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}

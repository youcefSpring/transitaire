<?php

namespace App\Models;

use App\Enums\DocumentCategorie;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['documentable_type', 'documentable_id', 'categorie', 'nom_original', 'chemin', 'mime_type', 'taille', 'televerse_par'])]
class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    protected function casts(): array
    {
        return [
            'categorie' => DocumentCategorie::class,
            'taille' => 'integer',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function televersePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'televerse_par');
    }
}

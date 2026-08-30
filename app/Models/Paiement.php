<?php

namespace App\Models;

use App\Enums\Devise;
use App\Enums\PaiementMode;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['client_id', 'document_id', 'mode', 'montant', 'devise', 'date_paiement', 'reference', 'created_by'])]
class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paiements';

    protected function casts(): array
    {
        return [
            'mode' => PaiementMode::class,
            'devise' => Devise::class,
            'montant' => 'decimal:2',
            'date_paiement' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DocumentCommercial::class, 'document_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

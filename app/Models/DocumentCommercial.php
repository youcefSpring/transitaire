<?php

namespace App\Models;

use App\Enums\Devise;
use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['type', 'numero', 'client_id', 'dossier_id', 'devise', 'total_prestations', 'total_frais', 'total_taxes', 'remise', 'montant_total', 'statut', 'date_emission', 'date_echeance', 'created_by'])]
class DocumentCommercial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents_commerciaux';

    protected function casts(): array
    {
        return [
            'type' => DocumentCommercialType::class,
            'devise' => Devise::class,
            'statut' => DocumentCommercialStatut::class,
            'total_prestations' => 'decimal:2',
            'total_frais' => 'decimal:2',
            'total_taxes' => 'decimal:2',
            'remise' => 'decimal:2',
            'montant_total' => 'decimal:2',
            'date_emission' => 'date',
            'date_echeance' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LignePrestation::class, 'document_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'document_id');
    }
}

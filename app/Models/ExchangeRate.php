<?php

namespace App\Models;

use App\Enums\Devise;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['devise', 'taux_dzd', 'date_taux', 'created_by'])]
class ExchangeRate extends Model
{
    use HasFactory;

    protected $table = 'exchange_rates';

    protected function casts(): array
    {
        return [
            'devise' => Devise::class,
            'taux_dzd' => 'decimal:6',
            'date_taux' => 'date',
        ];
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

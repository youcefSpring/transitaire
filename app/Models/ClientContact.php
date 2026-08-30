<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['client_id', 'nom', 'fonction', 'telephone', 'email', 'notes'])]
class ClientContact extends Model
{
    use HasFactory;

    protected $table = 'client_contacts';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

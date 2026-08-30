<?php

namespace App\Http\Requests;

use App\Enums\Devise;
use App\Enums\DocumentCommercialType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentCommercialStoreRequest extends FormRequest
{
    private const CATEGORIES_CLIENT = [
        'transit', 'dedouanement', 'manutention', 'transport', 'stockage',
        'frais_portuaires', 'frais_administratifs', 'autres_prestations',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(DocumentCommercialType::class)],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'dossier_id' => ['nullable', 'integer', 'exists:dossiers,id'],
            'devise' => ['required', Rule::enum(Devise::class)],
            'date_emission' => ['nullable', 'date'],
            'date_echeance' => ['nullable', 'date', 'after_or_equal:date_emission'],
            'total_taxes' => ['required', 'numeric', 'min:0'],
            'remise' => ['required', 'numeric', 'min:0'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.designation' => ['required', 'string', 'max:255'],
            'lignes.*.categorie' => ['required', 'string', 'in:'.implode(',', self::CATEGORIES_CLIENT)],
            'lignes.*.quantite' => ['required', 'numeric', 'min:0'],
            'lignes.*.prix_unitaire' => ['required', 'numeric', 'min:0'],
        ];
    }
}

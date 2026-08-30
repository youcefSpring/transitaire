<?php

namespace App\Http\Requests;

use App\Enums\Devise;
use App\Enums\PaiementMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaiementStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'document_id' => ['nullable', 'integer', 'exists:documents_commerciaux,id'],
            'mode' => ['required', Rule::enum(PaiementMode::class)],
            'montant' => ['required', 'numeric', 'min:0'],
            'devise' => ['required', Rule::enum(Devise::class)],
            'date_paiement' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }
}

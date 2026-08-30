<?php

namespace App\Http\Requests;

use App\Enums\DocumentCommercialStatut;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentCommercialStatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => ['required', Rule::enum(DocumentCommercialStatut::class)],
        ];
    }
}

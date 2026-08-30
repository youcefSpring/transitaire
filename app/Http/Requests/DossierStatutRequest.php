<?php

namespace App\Http\Requests;

use App\Enums\DossierStatut;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DossierStatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => ['required', Rule::enum(DossierStatut::class)],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarchandiseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'designation' => ['required', 'string', 'max:255'],
            'quantite' => ['required', 'numeric', 'min:0'],
            'unite' => ['required', 'string', 'max:50'],
            'nombre_colis' => ['required', 'integer', 'min:0'],
            'poids' => ['required', 'numeric', 'min:0'],
            'volume' => ['required', 'numeric', 'min:0'],
            'valeur' => ['required', 'numeric', 'min:0'],
            'pays_origine' => ['required', 'string', 'max:100'],
            'code_tarifaire' => ['required', 'string', 'max:50'],
            'infos_complementaires' => ['nullable', 'string'],
        ];
    }
}

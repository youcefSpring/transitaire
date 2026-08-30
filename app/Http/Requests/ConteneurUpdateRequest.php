<?php

namespace App\Http\Requests;

use App\Enums\ConteneurStatut;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConteneurUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => ['required', Rule::enum(ConteneurStatut::class)],
            'date_sortie' => [
                'nullable',
                'date',
                'required_if:statut,'.ConteneurStatut::Sorti->value,
                'required_if:statut,'.ConteneurStatut::Livre->value,
                'required_if:statut,'.ConteneurStatut::Retourne->value,
            ],
            'date_retour' => [
                'nullable',
                'date',
                'after_or_equal:date_sortie',
                'required_if:statut,'.ConteneurStatut::Retourne->value,
            ],
        ];
    }
}

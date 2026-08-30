<?php

namespace App\Http\Requests;

use App\Enums\Devise;
use App\Enums\ModeTransport;
use App\Enums\TypeOperation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DossierUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'type' => ['required', Rule::enum(TypeOperation::class)],
            'mode_transport' => ['required', Rule::enum(ModeTransport::class)],
            'port_aeroport' => ['required', 'string', 'max:255'],
            'fournisseur_destinataire' => ['required', 'string', 'max:255'],
            'date_arrivee_prevue' => ['required', 'date'],
            'date_arrivee_reelle' => ['nullable', 'date', 'after_or_equal:date_arrivee_prevue'],
            'numero_bl_awb' => ['required', 'string', 'max:255'],
            'nombre_colis' => ['required', 'integer', 'min:0'],
            'poids' => ['required', 'numeric', 'min:0'],
            'volume' => ['required', 'numeric', 'min:0'],
            'nature_marchandise' => ['required', 'string', 'max:255'],
            'valeur_declaree' => ['required', 'numeric', 'min:0'],
            'devise' => ['required', Rule::enum(Devise::class)],
            'incoterm' => ['required', 'string', 'max:10'],
        ];
    }
}

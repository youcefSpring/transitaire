<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConteneurStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero' => ['required', 'string', 'regex:/^[A-Z]{4}[0-9]{7}$/', 'unique:conteneurs,numero'],
            'type' => ['required', 'string', 'max:50'],
            'numero_bl' => ['required', 'string', 'max:255'],
            'navire' => ['nullable', 'string', 'max:255'],
            'port_depart' => ['required', 'string', 'max:255'],
            'port_arrivee' => ['required', 'string', 'max:255'],
            'date_eta' => ['required', 'date'],
            'date_ata' => ['nullable', 'date', 'after_or_equal:date_eta'],
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'dossier_id' => ['required', 'integer', 'exists:dossiers,id'],
        ];
    }
}

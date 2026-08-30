<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LivraisonStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dossier_id' => ['required', 'integer', 'exists:dossiers,id'],
            'camion_id' => ['nullable', 'integer', 'exists:camions,id', 'required_without:transporteur_externe_id'],
            'transporteur_externe_id' => ['nullable', 'integer', 'exists:fournisseurs,id'],
            'chauffeur_id' => ['nullable', 'integer', 'exists:chauffeurs,id'],
            'lieu_chargement' => ['required', 'string', 'max:255'],
            'entrepot' => ['nullable', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'date_heure_chargement' => ['required', 'date'],
            'date_heure_livraison' => ['nullable', 'date', 'after_or_equal:date_heure_chargement'],
            'frais_transport' => ['required', 'numeric', 'min:0'],
            'bon_livraison' => ['nullable', 'string', 'max:100'],
        ];
    }
}

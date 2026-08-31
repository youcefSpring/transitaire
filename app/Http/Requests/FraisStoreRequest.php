<?php

namespace App\Http\Requests;

use App\Enums\Devise;
use App\Enums\FraisCategorie;
use App\Enums\FraisSens;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FraisStoreRequest extends FormRequest
{
    private const CATEGORIES_CLIENT = [
        'transit', 'dedouanement', 'manutention', 'transport', 'stockage',
        'frais_portuaires', 'frais_administratifs', 'autres_prestations',
    ];

    private const CATEGORIES_TRANSITAIRE = [
        'manutention', 'transport', 'transporteur', 'port',
        'fournisseurs', 'prestataires', 'autres_depenses',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sens' => ['required', Rule::enum(FraisSens::class)],
            'categorie' => [
                'required',
                Rule::enum(FraisCategorie::class),
                function (string $attribute, mixed $value, Closure $fail) {
                    $valides = $this->input('sens') === FraisSens::SupporteTransitaire->value
                        ? self::CATEGORIES_TRANSITAIRE
                        : self::CATEGORIES_CLIENT;

                    if (! in_array($value, $valides, true)) {
                        $fail(__('app.messages.categorie_frais_invalide'));
                    }
                },
            ],
            'libelle' => ['nullable', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:0'],
            'devise' => ['required', Rule::enum(Devise::class)],
            'fournisseur_id' => [
                'nullable',
                'integer',
                'exists:fournisseurs,id',
                'required_if:sens,'.FraisSens::SupporteTransitaire->value,
            ],
            'date_frais' => ['required', 'date'],
        ];
    }
}

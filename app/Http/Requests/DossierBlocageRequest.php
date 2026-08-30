<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class DossierBlocageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bloque' => ['required', 'boolean'],
            'raison' => [
                'nullable',
                'string',
                'max:1000',
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($this->boolean('bloque') && blank($value)) {
                        $fail('La raison du blocage est obligatoire (§2).');
                    }
                },
            ],
        ];
    }
}

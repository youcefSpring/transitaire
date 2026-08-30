<?php

namespace App\Http\Requests;

use App\Enums\FournisseurType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FournisseurStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(FournisseurType::class)],
            'adresse' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
        ];
    }
}

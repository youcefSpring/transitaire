<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientContactStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'fonction' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'regex:/^\+213[0-9]{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

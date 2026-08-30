<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raison_sociale' => ['required', 'string', 'max:255'],
            'nif' => ['required', 'string', 'regex:/^[0-9]{15}$/', 'unique:clients,nif'],
            'nis' => ['required', 'string', 'regex:/^[0-9]{15}$/', 'unique:clients,nis'],
            'rc' => ['required', 'string', 'regex:/^[0-9]{2}\/[0-9]{2}-[0-9]{7,8}[A-Z][0-9]{2}$/', 'unique:clients,rc'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'regex:/^\+213[0-9]{9}$/'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
            'conditions_paiement' => ['required', 'string', 'max:255'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.nom' => ['required_with:contacts', 'string', 'max:255'],
            'contacts.*.fonction' => ['nullable', 'string', 'max:255'],
            'contacts.*.telephone' => ['required_with:contacts', 'string', 'regex:/^\+213[0-9]{9}$/'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
        ];
    }
}

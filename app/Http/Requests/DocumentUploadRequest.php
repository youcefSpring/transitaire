<?php

namespace App\Http\Requests;

use App\Enums\DocumentCategorie;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fichier' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png'],
            'categorie' => ['required', Rule::enum(DocumentCategorie::class)],
        ];
    }
}

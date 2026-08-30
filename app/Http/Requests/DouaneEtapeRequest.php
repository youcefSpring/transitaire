<?php

namespace App\Http\Requests;

use App\Enums\DouaneEtape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DouaneEtapeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'etape' => ['required', Rule::enum(DouaneEtape::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}

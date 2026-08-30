<?php

namespace App\Http\Requests;

use App\Enums\UserProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'profile' => ['required', Rule::enum(UserProfile::class)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'role' => ['required', Rule::in(['fiu_admin', 'fiu_reviewer', 'institution_user'])],
            'reporting_institution_id' => ['nullable', 'exists:reporting_institutions,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

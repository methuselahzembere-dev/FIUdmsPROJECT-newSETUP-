<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTechnicalComplianceFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isFiuUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('technical_compliance_folders', 'name')],
            'description' => ['nullable', 'string', 'max:2000'],
            'institution_ids' => ['nullable', 'array'],
            'institution_ids.*' => ['integer', 'exists:reporting_institutions,id'],
        ];
    }
}

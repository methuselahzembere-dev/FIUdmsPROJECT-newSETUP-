<?php

namespace App\Http\Requests;

use App\Models\TechnicalComplianceFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTechnicalComplianceDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'technical_compliance_folder_id' => [
                'required',
                'integer',
                Rule::exists('technical_compliance_folders', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'document' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,csv,png,jpg,jpeg'],
        ];
    }

    public function folder(): TechnicalComplianceFolder
    {
        return TechnicalComplianceFolder::query()->findOrFail($this->integer('technical_compliance_folder_id'));
    }
}

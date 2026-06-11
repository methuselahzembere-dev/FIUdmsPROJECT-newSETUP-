<?php

namespace App\Http\Requests\Fiu;

use App\Models\EffectivenessImmediateOutcome;
use App\Models\EffectivenessSubImmediateOutcome;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEffectivenessDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'immediate_outcome_id' => [
                'required',
                'integer',
                Rule::exists((new EffectivenessImmediateOutcome())->getTable(), 'id'),
            ],
            'effectiveness_sub_io_id' => [
                'required',
                'integer',
                Rule::exists((new EffectivenessSubImmediateOutcome())->getTable(), 'id'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'reporting_institution' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['logged', 'submitted', 'under_review', 'revision_requested', 'approved', 'archived'])],
            'date_logged' => ['required', 'date'],
            'document_date' => ['nullable', 'date', 'before_or_equal:today'],
            'remarks' => ['nullable', 'string', 'max:5000'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', 'max:10240'],
            'external_file_name' => ['nullable', 'string', 'max:255'],
            'external_file_path' => ['nullable', 'string', 'max:2048'],
            'disk' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $subOutcome = EffectivenessSubImmediateOutcome::query()
                ->find($this->integer('effectiveness_sub_io_id'));

            if (! $subOutcome) {
                return;
            }

            if ((int) $subOutcome->immediate_outcome_id !== $this->integer('immediate_outcome_id')) {
                $validator->errors()->add('effectiveness_sub_io_id', 'The selected sub-IO does not belong to the selected main IO.');
            }

            if (! $this->hasFile('document_file') && blank($this->input('external_file_name')) && blank($this->input('external_file_path'))) {
                $validator->errors()->add('document_file', 'Upload a file or provide existing file details.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('title') && ! $this->filled('name')) {
            $this->merge(['name' => $this->input('title')]);
        }

        if (! $this->filled('disk')) {
            $this->merge(['disk' => 'public']);
        }
    }
}
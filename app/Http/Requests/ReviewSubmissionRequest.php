<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                Submission::STATUS_UNDER_REVIEW,
                Submission::STATUS_REVISION_REQUESTED,
                Submission::STATUS_APPROVED,
            ])],
            'comments' => ['required', 'string'],
        ];
    }
}

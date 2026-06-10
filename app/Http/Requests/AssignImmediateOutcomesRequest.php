<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignImmediateOutcomesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'immediate_outcome_ids' => ['array'],
            'immediate_outcome_ids.*' => ['exists:immediate_outcomes,id'],
        ];
    }
}

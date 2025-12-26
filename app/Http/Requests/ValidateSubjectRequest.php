<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'department_head';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject,needs_correction',
            'feedback' => 'required|string|min:10',
            'corrections_needed' => 'required_if:action,needs_correction|array',
            'corrections_needed.*' => 'string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Validation action is required',
            'action.in' => 'Action must be approve, reject, or needs_correction',
            'feedback.required' => 'Validation feedback is required',
            'feedback.min' => 'Feedback must be at least 10 characters long',
            'corrections_needed.required_if' => 'Corrections list is required when action is needs_correction',
            'corrections_needed.array' => 'Corrections must be provided as a list',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'validator_id' => $this->user()->id,
            'validated_at' => now(),
        ]);
    }
}

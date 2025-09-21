<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->hasRole(['chef_master', 'admin_pfe']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                Rule::in(['approved', 'rejected', 'needs_correction'])
            ],
            'validation_notes' => [
                'nullable',
                'string',
                'max:1000',
                'required_if:action,rejected,needs_correction'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Validation action is required.',
            'action.in' => 'Invalid validation action. Must be approved, rejected, or needs_correction.',
            'validation_notes.required_if' => 'Validation notes are required when rejecting or requesting corrections.',
            'validation_notes.max' => 'Validation notes cannot exceed 1000 characters.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $subject = $this->route('subject');

            // Check if subject can be validated
            if ($subject && $subject->status !== 'submitted') {
                $validator->errors()->add('subject', 'Only submitted subjects can be validated.');
            }

            // Check department authorization
            $user = auth()->user();
            if ($subject && $user && $user->hasRole('chef_master')) {
                if ($user->department !== $subject->supervisor->department) {
                    $validator->errors()->add('authorization', 'You can only validate subjects from your department.');
                }
            }
        });
    }
}
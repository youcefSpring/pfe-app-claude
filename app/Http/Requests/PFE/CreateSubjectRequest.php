<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->hasRole('teacher') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:200',
                'min:10'
            ],
            'description' => [
                'required',
                'string',
                'min:100'
            ],
            'keywords' => [
                'required',
                'array',
                'min:3'
            ],
            'keywords.*' => [
                'required',
                'string',
                'max:50'
            ],
            'required_tools' => [
                'nullable',
                'array'
            ],
            'required_tools.*' => [
                'string',
                'max:100'
            ],
            'max_teams' => [
                'required',
                'integer',
                'min:1',
                'max:3'
            ],
            'external_supervisor' => [
                'nullable',
                'string',
                'max:100'
            ],
            'external_company' => [
                'nullable',
                'string',
                'max:100'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'Subject title must be at least 10 characters.',
            'title.max' => 'Subject title cannot exceed 200 characters.',
            'description.min' => 'Subject description must be at least 100 characters.',
            'keywords.required' => 'At least 3 keywords are required.',
            'keywords.min' => 'At least 3 keywords are required.',
            'keywords.*.required' => 'Each keyword is required.',
            'keywords.*.max' => 'Each keyword cannot exceed 50 characters.',
            'max_teams.min' => 'At least 1 team must be allowed.',
            'max_teams.max' => 'Maximum 3 teams are allowed per subject.',
            'external_supervisor.max' => 'External supervisor name cannot exceed 100 characters.',
            'external_company.max' => 'External company name cannot exceed 100 characters.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and prepare keywords array
        if ($this->has('keywords')) {
            $keywords = $this->keywords;
            if (is_string($keywords)) {
                // Convert comma-separated string to array
                $keywords = array_map('trim', explode(',', $keywords));
            }
            // Remove empty keywords
            $keywords = array_filter($keywords, function($keyword) {
                return !empty(trim($keyword));
            });

            $this->merge([
                'keywords' => array_values($keywords)
            ]);
        }

        // Clean and prepare required_tools array
        if ($this->has('required_tools')) {
            $tools = $this->required_tools;
            if (is_string($tools)) {
                $tools = array_map('trim', explode(',', $tools));
            }
            $tools = array_filter($tools, function($tool) {
                return !empty(trim($tool));
            });

            $this->merge([
                'required_tools' => array_values($tools)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check for duplicate keywords
            $keywords = $this->keywords ?? [];
            if (count($keywords) !== count(array_unique(array_map('strtolower', $keywords)))) {
                $validator->errors()->add('keywords', 'Keywords must be unique.');
            }
        });
    }
}
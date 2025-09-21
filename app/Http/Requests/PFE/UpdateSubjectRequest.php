<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Subject;

class UpdateSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $subject = $this->route('subject');
        $user = auth()->user();

        // Only the supervisor can edit their own subjects
        if ($subject && $user) {
            return $user->id === $subject->supervisor_id || $user->hasRole(['chef_master', 'admin_pfe']);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $subject = $this->route('subject');

        // If subject is already assigned, only allow limited updates
        $isAssigned = $subject && $subject->projects()->exists();

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:200',
                'min:10',
                $isAssigned ? 'prohibited' : ''
            ],
            'description' => [
                'sometimes',
                'required',
                'string',
                'min:100'
            ],
            'keywords' => [
                'sometimes',
                'required',
                'array',
                'min:3',
                $isAssigned ? 'prohibited' : ''
            ],
            'keywords.*' => [
                'required',
                'string',
                'max:50'
            ],
            'required_tools' => [
                'sometimes',
                'nullable',
                'array'
            ],
            'required_tools.*' => [
                'string',
                'max:100'
            ],
            'max_teams' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:3',
                $isAssigned ? 'prohibited' : ''
            ],
            'external_supervisor' => [
                'sometimes',
                'nullable',
                'string',
                'max:100'
            ],
            'external_company' => [
                'sometimes',
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
            'title.prohibited' => 'Cannot modify title of assigned subjects.',
            'keywords.prohibited' => 'Cannot modify keywords of assigned subjects.',
            'max_teams.prohibited' => 'Cannot modify max teams of assigned subjects.',
            'title.min' => 'Subject title must be at least 10 characters.',
            'title.max' => 'Subject title cannot exceed 200 characters.',
            'description.min' => 'Subject description must be at least 100 characters.',
            'keywords.required' => 'At least 3 keywords are required.',
            'keywords.min' => 'At least 3 keywords are required.',
            'keywords.*.required' => 'Each keyword is required.',
            'keywords.*.max' => 'Each keyword cannot exceed 50 characters.',
            'max_teams.min' => 'At least 1 team must be allowed.',
            'max_teams.max' => 'Maximum 3 teams are allowed per subject.'
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
                $keywords = array_map('trim', explode(',', $keywords));
            }
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
            $subject = $this->route('subject');

            // Check if subject can be modified based on status
            if ($subject && in_array($subject->status, ['published']) && $subject->projects()->exists()) {
                $validator->errors()->add('subject', 'Cannot modify assigned subjects.');
            }

            // Check for duplicate keywords
            if ($this->has('keywords')) {
                $keywords = $this->keywords ?? [];
                if (count($keywords) !== count(array_unique(array_map('strtolower', $keywords)))) {
                    $validator->errors()->add('keywords', 'Keywords must be unique.');
                }
            }
        });
    }
}
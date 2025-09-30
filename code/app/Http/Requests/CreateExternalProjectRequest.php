<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExternalProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $team = $this->route('team');

        return $this->user() &&
               $this->user()->role === 'student' &&
               ($team->creator_id === $this->user()->id || $team->isLeader($this->user())) &&
               $team->status === 'complete' &&
               !$team->subject_id &&
               !$team->externalProject;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'project_description' => 'required|string|min:100',
            'objectives' => 'required|string|min:50',
            'technologies' => 'required|string',
            'expected_outcomes' => 'required|string|min:50',
            'internship_duration' => 'nullable|integer|min:1|max:12',
            'company_address' => 'nullable|string|max:500',
            'proposal_document' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required',
            'company.required' => 'Company name is required',
            'contact_person.required' => 'Contact person name is required',
            'contact_email.required' => 'Contact email is required',
            'contact_email.email' => 'Please provide a valid email address',
            'project_description.required' => 'Project description is required',
            'project_description.min' => 'Project description must be at least 100 characters',
            'objectives.required' => 'Project objectives are required',
            'objectives.min' => 'Objectives must be at least 50 characters',
            'technologies.required' => 'Technologies/tools are required',
            'expected_outcomes.required' => 'Expected outcomes are required',
            'expected_outcomes.min' => 'Expected outcomes must be at least 50 characters',
            'internship_duration.integer' => 'Duration must be a number',
            'internship_duration.min' => 'Duration must be at least 1 month',
            'internship_duration.max' => 'Duration cannot exceed 12 months',
            'proposal_document.file' => 'Proposal must be a file',
            'proposal_document.mimes' => 'Proposal must be a PDF file',
            'proposal_document.max' => 'Proposal file cannot exceed 10MB',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $team = $this->route('team');

        $this->merge([
            'team_id' => $team->id,
            'status' => 'pending_approval',
            'submitted_at' => now(),
        ]);
    }
}

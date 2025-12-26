<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:subjects,title',
            'description' => 'required|string|min:50',
            'keywords' => 'required|string',
            'tools' => 'required|string',
            'plan' => 'required|string|min:100',
            'grade' => 'required|in:licence,master,phd',
            'type' => 'required|in:internal,collaboration',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'required_skills' => 'nullable|string',
            'expected_outcomes' => 'nullable|string',
            'max_teams' => 'nullable|integer|min:1|max:5',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Subject title is required',
            'title.unique' => 'A subject with this title already exists',
            'description.required' => 'Subject description is required',
            'description.min' => 'Description must be at least 50 characters long',
            'keywords.required' => 'Keywords are required',
            'tools.required' => 'Tools and technologies are required',
            'plan.required' => 'Project plan is required',
            'plan.min' => 'Project plan must be at least 100 characters long',
            'grade.required' => 'Academic grade is required',
            'grade.in' => 'Grade must be licence, master, or phd',
            'type.required' => 'Subject type is required',
            'type.in' => 'Type must be internal or collaboration',
            'difficulty_level.required' => 'Difficulty level is required',
            'difficulty_level.in' => 'Difficulty level must be easy, medium, or hard',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'teacher_id' => $this->user()->id,
        ]);
    }
}

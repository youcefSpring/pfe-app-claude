<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled by middleware/controllers
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'first_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/' // Allow letters, accents, and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
            ],
            'phone' => [
                'nullable',
                'regex:/^\+213[0-9]{9}$/' // Algerian phone format
            ],
            'student_id' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,student_id',
                'required_if:role,student'
            ],
            'department' => [
                'required',
                'string',
                Rule::in(['informatique', 'mathematiques', 'physique'])
            ],
            'role' => [
                'required',
                'string',
                Rule::in(['student', 'teacher', 'chef_master', 'admin_pfe', 'externe'])
            ],
            'avatar_path' => [
                'nullable',
                'string',
                'max:255'
            ],
            'is_active' => [
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.regex' => 'Phone number must be in Algerian format (+213xxxxxxxxx).',
            'student_id.unique' => 'This student ID is already registered.',
            'student_id.required_if' => 'Student ID is required for student accounts.',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'department.in' => 'Department must be one of: informatique, mathematiques, physique.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true)
        ]);
    }
}
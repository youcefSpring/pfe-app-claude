<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : $this->user_id;

        return [
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
                'max:255'
            ],
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'confirmed'
            ],
            'first_name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
            ],
            'last_name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/'
            ],
            'phone' => [
                'nullable',
                'regex:/^\+213[0-9]{9}$/'
            ],
            'student_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($userId)
            ],
            'department' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['informatique', 'mathematiques', 'physique'])
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
            'email.unique' => 'This email address is already taken by another user.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.regex' => 'Phone number must be in Algerian format (+213xxxxxxxxx).',
            'student_id.unique' => 'This student ID is already taken by another user.',
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
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active')
            ]);
        }
    }
}
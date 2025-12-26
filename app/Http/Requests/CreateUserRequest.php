<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,teacher,department_head,admin,external_supervisor',
            'department' => 'required_if:role,student,teacher,department_head|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',

            // Student-specific fields
            'matricule' => 'required_if:role,student|string|max:50|unique:users,matricule',
            'grade' => 'required_if:role,student|in:licence,master,phd',
            'enrollment_year' => 'required_if:role,student|integer|min:2020|max:' . (date('Y') + 1),

            // Teacher-specific fields
            'title' => 'required_if:role,teacher,department_head|string|max:100',
            'speciality' => 'required_if:role,teacher,department_head|string|max:255',
            'office_location' => 'nullable|string|max:255',

            // External supervisor fields
            'company' => 'required_if:role,external_supervisor|string|max:255',
            'position' => 'required_if:role,external_supervisor|string|max:255',
            'expertise_areas' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters long',
            'password.confirmed' => 'Password confirmation does not match',
            'role.required' => 'User role is required',
            'role.in' => 'Invalid user role selected',
            'department.required_if' => 'Department is required for this role',
            'matricule.required_if' => 'Student ID (matricule) is required for students',
            'matricule.unique' => 'This student ID is already registered',
            'grade.required_if' => 'Academic grade is required for students',
            'grade.in' => 'Grade must be licence, master, or phd',
            'enrollment_year.required_if' => 'Enrollment year is required for students',
            'enrollment_year.min' => 'Invalid enrollment year',
            'enrollment_year.max' => 'Enrollment year cannot be in the future',
            'title.required_if' => 'Academic title is required for teachers',
            'speciality.required_if' => 'Speciality is required for teachers',
            'company.required_if' => 'Company name is required for external supervisors',
            'position.required_if' => 'Position is required for external supervisors',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $role = $this->input('role');
            $department = $this->input('department');

            // Validate department head uniqueness
            if ($role === 'department_head' && $department) {
                $existingHead = \App\Models\User::where('role', 'department_head')
                    ->where('department', $department)
                    ->first();

                if ($existingHead) {
                    $validator->errors()->add('role', 'This department already has a department head');
                }
            }

            // Validate enrollment year for current academic year
            if ($role === 'student') {
                $currentAcademicYear = \App\Services\PfeHelper::getAcademicYear();
                $enrollmentYear = $this->input('enrollment_year');

                if ($enrollmentYear) {
                    $yearDiff = date('Y') - $enrollmentYear;
                    $grade = $this->input('grade');

                    // Validate reasonable year range for grade
                    $maxYears = ['licence' => 3, 'master' => 5, 'phd' => 8];
                    if (isset($maxYears[$grade]) && $yearDiff > $maxYears[$grade]) {
                        $validator->errors()->add('enrollment_year', 'Enrollment year is too old for selected grade');
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate matricule for students if not provided
        if ($this->input('role') === 'student' && !$this->input('matricule')) {
            $matricule = \App\Services\PfeHelper::generateMatricule(
                'student',
                $this->input('department')
            );
            $this->merge(['matricule' => $matricule]);
        }

        // Hash the password
        if ($this->input('password')) {
            $this->merge([
                'password' => bcrypt($this->input('password')),
            ]);
        }
    }
}

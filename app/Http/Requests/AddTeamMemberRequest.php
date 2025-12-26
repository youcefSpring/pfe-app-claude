<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddTeamMemberRequest extends FormRequest
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
               $team->status === 'forming';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $team = $this->route('team');

        return [
            'student_id' => [
                'required',
                'exists:users,id',
                Rule::unique('team_members')->where(function ($query) use ($team) {
                    return $query->where('team_id', $team->id);
                }),
            ],
            'role' => 'required|in:member,leader',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Student selection is required',
            'student_id.exists' => 'Selected student does not exist',
            'student_id.unique' => 'This student is already a member of this team',
            'role.required' => 'Member role is required',
            'role.in' => 'Role must be either member or leader',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $team = $this->route('team');
            $studentId = $this->input('student_id');
            $role = $this->input('role');

            // Check if student has same academic level
            $student = \App\Models\User::find($studentId);
            if ($student) {
                $teamGrade = $team->getAcademicLevel();
                if ($teamGrade && $student->grade !== $teamGrade) {
                    $validator->errors()->add('student_id', 'Student must be from the same academic level as team members');
                }

                // Check if student already has an active team
                if ($student->hasActiveTeam()) {
                    $validator->errors()->add('student_id', 'Student already belongs to an active team');
                }
            }

            // Check leader constraints
            if ($role === 'leader') {
                if ($team->hasLeader()) {
                    $validator->errors()->add('role', 'Team already has a leader');
                }
            }

            // Check team size limits
            $currentSize = $team->members()->count();
            $maxSize = $team->getMaxTeamSize();
            if ($currentSize >= $maxSize) {
                $validator->errors()->add('team', 'Team has reached maximum size');
            }
        });
    }
}

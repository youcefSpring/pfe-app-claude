<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Subject;
use App\Models\Team;
use App\Models\User;

class CreateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->hasRole(['admin_pfe', 'chef_master']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'team_id' => [
                'required',
                'integer',
                'exists:teams,id'
            ],
            'supervisor_id' => [
                'required',
                'integer',
                'exists:users,id'
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
            ],
            'start_date' => [
                'required',
                'date',
                'after:today'
            ],
            'expected_end_date' => [
                'required',
                'date',
                'after:start_date'
            ],
            'comments' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'Subject is required.',
            'subject_id.exists' => 'Selected subject does not exist.',
            'team_id.required' => 'Team is required.',
            'team_id.exists' => 'Selected team does not exist.',
            'supervisor_id.required' => 'Supervisor is required.',
            'supervisor_id.exists' => 'Selected supervisor does not exist.',
            'start_date.required' => 'Start date is required.',
            'start_date.after' => 'Start date must be in the future.',
            'expected_end_date.required' => 'Expected end date is required.',
            'expected_end_date.after' => 'Expected end date must be after start date.',
            'external_supervisor.max' => 'External supervisor name cannot exceed 100 characters.',
            'external_company.max' => 'External company name cannot exceed 100 characters.',
            'comments.max' => 'Comments cannot exceed 1000 characters.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $subjectId = $this->subject_id;
            $teamId = $this->team_id;
            $supervisorId = $this->supervisor_id;

            // Check if subject is available
            if ($subjectId) {
                $subject = Subject::find($subjectId);
                if ($subject) {
                    if ($subject->status !== 'published') {
                        $validator->errors()->add('subject_id', 'Subject must be published to be assigned.');
                    }

                    if ($subject->projects()->exists()) {
                        $validator->errors()->add('subject_id', 'Subject is already assigned to another team.');
                    }
                }
            }

            // Check if team is available
            if ($teamId) {
                $team = Team::find($teamId);
                if ($team) {
                    if ($team->status !== 'validated') {
                        $validator->errors()->add('team_id', 'Team must be validated to be assigned to a project.');
                    }

                    if ($team->project()->exists()) {
                        $validator->errors()->add('team_id', 'Team is already assigned to another project.');
                    }
                }
            }

            // Check supervisor eligibility
            if ($supervisorId) {
                $supervisor = User::find($supervisorId);
                if ($supervisor) {
                    if (!$supervisor->hasRole('teacher')) {
                        $validator->errors()->add('supervisor_id', 'Supervisor must be a teacher.');
                    }

                    // Check supervisor workload
                    $currentProjects = $supervisor->supervisedPfeProjects()->count();
                    $maxProjects = config('pfe.max_projects_per_supervisor', 8);
                    if ($currentProjects >= $maxProjects) {
                        $validator->errors()->add('supervisor_id', "Supervisor already has the maximum number of projects ({$maxProjects}).");
                    }
                }
            }

            // Validate project duration
            if ($this->start_date && $this->expected_end_date) {
                $startDate = \Carbon\Carbon::parse($this->start_date);
                $endDate = \Carbon\Carbon::parse($this->expected_end_date);
                $duration = $startDate->diffInMonths($endDate);

                if ($duration < 3) {
                    $validator->errors()->add('expected_end_date', 'Project duration must be at least 3 months.');
                }

                if ($duration > 12) {
                    $validator->errors()->add('expected_end_date', 'Project duration cannot exceed 12 months.');
                }
            }

            // Check department compatibility
            if ($subjectId && $teamId) {
                $subject = Subject::with('supervisor')->find($subjectId);
                $team = Team::with('leader')->find($teamId);

                if ($subject && $team) {
                    if ($subject->supervisor->department !== $team->leader->department) {
                        $validator->errors()->add('compatibility', 'Subject and team must be from the same department.');
                    }
                }
            }
        });
    }
}
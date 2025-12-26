<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'department_head']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'supervisor_id' => 'required|exists:users,id',
            'co_supervisor_id' => 'nullable|exists:users,id|different:supervisor_id',
            'external_company' => 'nullable|string|max:255',
            'external_supervisor' => 'nullable|string|max:255',
            'objectives' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'timeline' => 'nullable|string',
            'resources' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'team_id.required' => 'Team selection is required',
            'team_id.exists' => 'Selected team does not exist',
            'supervisor_id.required' => 'Supervisor selection is required',
            'supervisor_id.exists' => 'Selected supervisor does not exist',
            'co_supervisor_id.exists' => 'Selected co-supervisor does not exist',
            'co_supervisor_id.different' => 'Co-supervisor must be different from supervisor',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $teamId = $this->input('team_id');
            $supervisorId = $this->input('supervisor_id');
            $coSupervisorId = $this->input('co_supervisor_id');

            if ($teamId) {
                $team = \App\Models\Team::find($teamId);

                if ($team) {
                    // Check if team is ready for project creation
                    if ($team->status !== 'assigned') {
                        $validator->errors()->add('team_id', 'Team must be assigned to a subject before project creation');
                    }

                    // Check if team already has a project
                    if ($team->project) {
                        $validator->errors()->add('team_id', 'Team already has an assigned project');
                    }
                }
            }

            if ($supervisorId) {
                $supervisor = \App\Models\User::find($supervisorId);

                if ($supervisor) {
                    // Check if user can supervise
                    if (!in_array($supervisor->role, ['teacher', 'external_supervisor'])) {
                        $validator->errors()->add('supervisor_id', 'Selected user cannot supervise projects');
                    }

                    // Check supervisor workload
                    if ($supervisor->role === 'teacher' && $supervisor->getCurrentWorkload() >= 5) {
                        $validator->errors()->add('supervisor_id', 'Supervisor has reached maximum capacity');
                    }
                }
            }

            if ($coSupervisorId) {
                $coSupervisor = \App\Models\User::find($coSupervisorId);

                if ($coSupervisor && !in_array($coSupervisor->role, ['teacher', 'external_supervisor'])) {
                    $validator->errors()->add('co_supervisor_id', 'Selected user cannot co-supervise projects');
                }
            }
        });
    }
}

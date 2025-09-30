<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ScheduleDefenseRequest extends FormRequest
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
            'project_id' => 'required|exists:projects,id',
            'defense_date' => 'required|date|after:today|before:' . now()->addMonths(6)->format('Y-m-d'),
            'defense_time' => 'required|date_format:H:i',
            'room_id' => 'required|exists:rooms,id',
            'duration' => 'required|integer|min:30|max:120',
            'jury_members' => 'required|array|min:3|max:5',
            'jury_members.*' => 'exists:users,id',
            'jury_roles' => 'required|array',
            'jury_roles.*' => 'in:president,examiner,supervisor',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Project selection is required',
            'project_id.exists' => 'Selected project does not exist',
            'defense_date.required' => 'Defense date is required',
            'defense_date.after' => 'Defense must be scheduled for a future date',
            'defense_date.before' => 'Defense cannot be scheduled more than 6 months in advance',
            'defense_time.required' => 'Defense time is required',
            'defense_time.date_format' => 'Please provide time in HH:MM format',
            'room_id.required' => 'Room selection is required',
            'room_id.exists' => 'Selected room does not exist',
            'duration.required' => 'Duration is required',
            'duration.min' => 'Defense duration must be at least 30 minutes',
            'duration.max' => 'Defense duration cannot exceed 2 hours',
            'jury_members.required' => 'Jury members are required',
            'jury_members.min' => 'At least 3 jury members are required',
            'jury_members.max' => 'Maximum 5 jury members allowed',
            'jury_members.*.exists' => 'One or more selected jury members do not exist',
            'jury_roles.required' => 'Jury roles are required',
            'jury_roles.*.in' => 'Invalid jury role specified',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $projectId = $this->input('project_id');
            $defenseDate = $this->input('defense_date');
            $defenseTime = $this->input('defense_time');
            $roomId = $this->input('room_id');
            $juryMembers = $this->input('jury_members', []);

            // Validate project status
            if ($projectId) {
                $project = \App\Models\Project::find($projectId);
                if ($project && $project->status !== 'submitted') {
                    $validator->errors()->add('project_id', 'Project must be submitted before scheduling defense');
                }
            }

            // Validate defense time constraints
            if ($defenseDate && $defenseTime) {
                $dateTime = Carbon::parse($defenseDate . ' ' . $defenseTime);

                // Check business hours (8 AM to 6 PM)
                $hour = $dateTime->hour;
                if ($hour < 8 || $hour >= 18) {
                    $validator->errors()->add('defense_time', 'Defenses must be scheduled between 8:00 AM and 6:00 PM');
                }

                // Check weekends
                if ($dateTime->isWeekend()) {
                    $validator->errors()->add('defense_date', 'Defenses cannot be scheduled on weekends');
                }
            }

            // Validate room availability
            if ($roomId && $defenseDate && $defenseTime) {
                $room = \App\Models\Room::find($roomId);
                if ($room && !$room->isAvailableAt($defenseDate, $defenseTime)) {
                    $validator->errors()->add('room_id', 'Room is not available at the requested time');
                }
            }

            // Validate jury availability
            if (!empty($juryMembers) && $defenseDate && $defenseTime) {
                $conflicts = \App\Models\Defense::where('defense_date', $defenseDate)
                    ->where('defense_time', $defenseTime)
                    ->whereHas('jury', function ($q) use ($juryMembers) {
                        $q->whereIn('teacher_id', $juryMembers);
                    })
                    ->exists();

                if ($conflicts) {
                    $validator->errors()->add('jury_members', 'One or more jury members are not available at the requested time');
                }
            }

            // Validate jury composition
            $juryRoles = $this->input('jury_roles', []);
            if (!empty($juryRoles)) {
                // Must have exactly one president
                $presidentCount = array_count_values($juryRoles)['president'] ?? 0;
                if ($presidentCount !== 1) {
                    $validator->errors()->add('jury_roles', 'Jury must have exactly one president');
                }

                // Check if supervisor is project supervisor
                if ($projectId && in_array('supervisor', $juryRoles)) {
                    $project = \App\Models\Project::find($projectId);
                    $supervisorIndex = array_search('supervisor', $juryRoles);

                    if ($project && $supervisorIndex !== false) {
                        $supervisorId = $juryMembers[$supervisorIndex] ?? null;
                        if ($supervisorId && $supervisorId != $project->supervisor_id) {
                            $validator->errors()->add('jury_roles', 'Supervisor in jury must be the project supervisor');
                        }
                    }
                }
            }
        });
    }
}

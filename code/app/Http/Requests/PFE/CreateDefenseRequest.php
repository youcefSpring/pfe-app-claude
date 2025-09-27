<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Defense;
use App\Models\User;
use Carbon\Carbon;

class CreateDefenseRequest extends FormRequest
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
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id'
            ],
            'room_id' => [
                'required',
                'integer',
                'exists:rooms,id'
            ],
            'defense_date' => [
                'required',
                'date',
                'after:today'
            ],
            'start_time' => [
                'required',
                'date_format:H:i'
            ],
            'duration' => [
                'required',
                'integer',
                'min:30',
                'max:120'
            ],
            'jury_president_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'jury_examiner_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'jury_supervisor_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'notes' => [
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
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'Selected project does not exist.',
            'room_id.required' => 'Room is required.',
            'room_id.exists' => 'Selected room does not exist.',
            'defense_date.required' => 'Defense date is required.',
            'defense_date.after' => 'Defense date must be in the future.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'duration.required' => 'Duration is required.',
            'duration.min' => 'Defense duration must be at least 30 minutes.',
            'duration.max' => 'Defense duration cannot exceed 120 minutes.',
            'jury_president_id.required' => 'Jury president is required.',
            'jury_president_id.exists' => 'Selected jury president does not exist.',
            'jury_examiner_id.required' => 'Jury examiner is required.',
            'jury_examiner_id.exists' => 'Selected jury examiner does not exist.',
            'jury_supervisor_id.required' => 'Jury supervisor is required.',
            'jury_supervisor_id.exists' => 'Selected jury supervisor does not exist.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Calculate end time based on start time and duration
        if ($this->has('start_time') && $this->has('duration')) {
            try {
                $startTime = Carbon::createFromFormat('H:i', $this->start_time);
                $endTime = $startTime->copy()->addMinutes($this->duration);

                $this->merge([
                    'end_time' => $endTime->format('H:i')
                ]);
            } catch (\Exception $e) {
                // Invalid time format, let validation handle it
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateProject($validator);
            $this->validateScheduling($validator);
            $this->validateJury($validator);
            $this->validateWorkingHours($validator);
        });
    }

    /**
     * Validate project eligibility for defense.
     */
    private function validateProject($validator): void
    {
        if ($this->project_id) {
            $project = \App\Models\PfeProject::find($this->project_id);

            if ($project) {
                if ($project->status !== 'ready_for_defense') {
                    $validator->errors()->add('project_id', 'Project must be ready for defense.');
                }

                if ($project->defense) {
                    $validator->errors()->add('project_id', 'Project already has a scheduled defense.');
                }

                // Check if final deliverable is submitted
                if (!$project->deliverables()->where('is_final_report', true)->exists()) {
                    $validator->errors()->add('project_id', 'Project must have a final report submitted.');
                }
            }
        }
    }

    /**
     * Validate scheduling constraints.
     */
    private function validateScheduling($validator): void
    {
        if ($this->defense_date && $this->start_time && $this->room_id) {
            $defenseDate = Carbon::parse($this->defense_date);

            // Check if it's a weekend
            if ($defenseDate->isWeekend()) {
                $validator->errors()->add('defense_date', 'Defenses cannot be scheduled on weekends.');
            }

            // Check room availability
            $startTime = $this->start_time;
            $endTime = $this->end_time ?? $this->start_time;

            $roomConflict = Defense::where('room_id', $this->room_id)
                ->where('defense_date', $this->defense_date)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                })
                ->exists();

            if ($roomConflict) {
                $validator->errors()->add('scheduling', 'Room is not available at the specified time.');
            }
        }
    }

    /**
     * Validate jury composition.
     */
    private function validateJury($validator): void
    {
        $juryIds = [
            $this->jury_president_id,
            $this->jury_examiner_id,
            $this->jury_supervisor_id
        ];

        // Check for duplicate jury members
        if (count($juryIds) !== count(array_unique(array_filter($juryIds)))) {
            $validator->errors()->add('jury', 'Jury members must be different people.');
        }

        // Validate each jury member
        foreach (['president', 'examiner', 'supervisor'] as $role) {
            $userId = $this->{"jury_{$role}_id"};
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    if (!$user->hasRole(['teacher', 'chef_master'])) {
                        $validator->errors()->add("jury_{$role}_id", "Jury {$role} must be a teacher or department head.");
                    }

                    // Check availability
                    if ($this->defense_date && $this->start_time) {
                        $conflict = Defense::where('defense_date', $this->defense_date)
                            ->where(function ($query) use ($userId) {
                                $query->where('jury_president_id', $userId)
                                    ->orWhere('jury_examiner_id', $userId)
                                    ->orWhere('jury_supervisor_id', $userId);
                            })
                            ->where(function ($query) {
                                $startTime = $this->start_time;
                                $endTime = $this->end_time ?? $this->start_time;
                                $query->whereBetween('start_time', [$startTime, $endTime])
                                    ->orWhereBetween('end_time', [$startTime, $endTime]);
                            })
                            ->exists();

                        if ($conflict) {
                            $validator->errors()->add("jury_{$role}_id", "Jury {$role} is not available at the specified time.");
                        }
                    }
                }
            }
        }

        // Validate supervisor assignment
        if ($this->project_id && $this->jury_supervisor_id) {
            $project = \App\Models\PfeProject::find($this->project_id);
            if ($project && $project->supervisor_id !== $this->jury_supervisor_id) {
                $validator->errors()->add('jury_supervisor_id', 'Jury supervisor must be the project supervisor.');
            }
        }
    }

    /**
     * Validate working hours.
     */
    private function validateWorkingHours($validator): void
    {
        if ($this->start_time) {
            try {
                $startTime = Carbon::createFromFormat('H:i', $this->start_time);
                $endTime = isset($this->end_time) ? Carbon::createFromFormat('H:i', $this->end_time) : $startTime->copy()->addMinutes($this->duration ?? 60);

                // Working hours: 8:00 - 18:00
                $workStart = Carbon::createFromFormat('H:i', '08:00');
                $workEnd = Carbon::createFromFormat('H:i', '18:00');

                if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                    $validator->errors()->add('start_time', 'Defense must be scheduled during working hours (08:00 - 18:00).');
                }
            } catch (\Exception $e) {
                // Time format error, already handled by validation rules
            }
        }
    }
}
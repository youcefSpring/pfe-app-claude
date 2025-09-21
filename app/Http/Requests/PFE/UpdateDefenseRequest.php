<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Defense;
use Carbon\Carbon;

class UpdateDefenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $defense = $this->route('defense');
        $user = auth()->user();

        if (!$defense || !$user) {
            return false;
        }

        // Admin and department heads can update defenses
        if ($user->hasRole(['admin_pfe', 'chef_master'])) {
            return true;
        }

        // Jury members can update grades and observations
        return in_array($user->id, [
            $defense->jury_president_id,
            $defense->jury_examiner_id,
            $defense->jury_supervisor_id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $defense = $this->route('defense');

        return [
            'defense_date' => [
                'sometimes',
                'required',
                'date',
                'after:today'
            ],
            'start_time' => [
                'sometimes',
                'required',
                'date_format:H:i'
            ],
            'duration' => [
                'sometimes',
                'required',
                'integer',
                'min:30',
                'max:120'
            ],
            'room_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:rooms,id'
            ],
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['scheduled', 'confirmed', 'rescheduled', 'in_progress', 'completed', 'archived'])
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'final_grade' => [
                'nullable',
                'numeric',
                'min:0',
                'max:20'
            ],
            'grade_president' => [
                'nullable',
                'numeric',
                'min:0',
                'max:20'
            ],
            'grade_examiner' => [
                'nullable',
                'numeric',
                'min:0',
                'max:20'
            ],
            'grade_supervisor' => [
                'nullable',
                'numeric',
                'min:0',
                'max:20'
            ],
            'observations' => [
                'nullable',
                'string',
                'max:2000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'defense_date.after' => 'Defense date must be in the future.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'duration.min' => 'Defense duration must be at least 30 minutes.',
            'duration.max' => 'Defense duration cannot exceed 120 minutes.',
            'room_id.exists' => 'Selected room does not exist.',
            'status.in' => 'Invalid defense status.',
            'final_grade.min' => 'Grade must be at least 0.',
            'final_grade.max' => 'Grade cannot exceed 20.',
            'grade_president.min' => 'President grade must be at least 0.',
            'grade_president.max' => 'President grade cannot exceed 20.',
            'grade_examiner.min' => 'Examiner grade must be at least 0.',
            'grade_examiner.max' => 'Examiner grade cannot exceed 20.',
            'grade_supervisor.min' => 'Supervisor grade must be at least 0.',
            'grade_supervisor.max' => 'Supervisor grade cannot exceed 20.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'observations.max' => 'Observations cannot exceed 2000 characters.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Calculate end time if start time and duration are provided
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

        // Calculate final grade if individual grades are provided
        if ($this->has('grade_president') && $this->has('grade_examiner') && $this->has('grade_supervisor')) {
            $grades = [
                $this->grade_president,
                $this->grade_examiner,
                $this->grade_supervisor
            ];

            // Remove null values and calculate average
            $validGrades = array_filter($grades, function($grade) {
                return $grade !== null && $grade !== '';
            });

            if (count($validGrades) === 3) {
                $finalGrade = array_sum($validGrades) / count($validGrades);
                $this->merge([
                    'final_grade' => round($finalGrade, 2)
                ]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $defense = $this->route('defense');

            if ($defense) {
                $this->validateStatusTransition($validator, $defense);
                $this->validateSchedulingUpdate($validator, $defense);
                $this->validateGradePermissions($validator, $defense);
            }
        });
    }

    /**
     * Validate status transitions.
     */
    private function validateStatusTransition($validator, $defense): void
    {
        if (!$this->has('status')) {
            return;
        }

        $currentStatus = $defense->status;
        $newStatus = $this->status;

        $allowedTransitions = [
            'scheduled' => ['confirmed', 'rescheduled'],
            'confirmed' => ['in_progress', 'rescheduled'],
            'rescheduled' => ['confirmed', 'in_progress'],
            'in_progress' => ['completed'],
            'completed' => ['archived'],
            'archived' => [] // Final status
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            $validator->errors()->add('status', 'Invalid current defense status.');
            return;
        }

        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $validator->errors()->add('status', "Cannot transition from '{$currentStatus}' to '{$newStatus}'.");
        }

        // Special validations
        if ($newStatus === 'completed') {
            if (!$this->has('final_grade') && !$defense->final_grade) {
                $validator->errors()->add('final_grade', 'Final grade is required to complete defense.');
            }
        }
    }

    /**
     * Validate scheduling updates.
     */
    private function validateSchedulingUpdate($validator, $defense): void
    {
        // Cannot reschedule completed or archived defenses
        if (in_array($defense->status, ['completed', 'archived'])) {
            if ($this->has('defense_date') || $this->has('start_time') || $this->has('room_id')) {
                $validator->errors()->add('scheduling', 'Cannot reschedule completed or archived defenses.');
            }
        }

        // Check minimum notice for rescheduling
        if ($this->has('defense_date') && $defense->status !== 'scheduled') {
            $newDate = Carbon::parse($this->defense_date);
            if ($newDate->diffInHours(now()) < 48) {
                $validator->errors()->add('defense_date', 'Defenses must be rescheduled with at least 48 hours notice.');
            }
        }

        // Validate room availability if changing room or time
        if (($this->has('room_id') || $this->has('defense_date') || $this->has('start_time')) && $defense->id) {
            $roomId = $this->room_id ?? $defense->room_id;
            $defenseDate = $this->defense_date ?? $defense->defense_date;
            $startTime = $this->start_time ?? $defense->start_time;
            $endTime = $this->end_time ?? $defense->end_time;

            $conflict = Defense::where('room_id', $roomId)
                ->where('defense_date', $defenseDate)
                ->where('id', '!=', $defense->id)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                              ->where('end_time', '>=', $endTime);
                        });
                })
                ->exists();

            if ($conflict) {
                $validator->errors()->add('scheduling', 'Room is not available at the specified time.');
            }
        }
    }

    /**
     * Validate grade permissions.
     */
    private function validateGradePermissions($validator, $defense): void
    {
        $user = auth()->user();

        // Only jury members can assign grades
        $gradeFields = ['grade_president', 'grade_examiner', 'grade_supervisor', 'final_grade'];
        $hasGradeUpdates = collect($gradeFields)->some(fn($field) => $this->has($field));

        if ($hasGradeUpdates) {
            $isJuryMember = in_array($user->id, [
                $defense->jury_president_id,
                $defense->jury_examiner_id,
                $defense->jury_supervisor_id
            ]);

            $isAdmin = $user->hasRole(['admin_pfe', 'chef_master']);

            if (!$isJuryMember && !$isAdmin) {
                $validator->errors()->add('grades', 'Only jury members can assign grades.');
            }

            // Specific role-based grade permissions
            if ($this->has('grade_president') && $user->id !== $defense->jury_president_id && !$isAdmin) {
                $validator->errors()->add('grade_president', 'Only the jury president can assign this grade.');
            }

            if ($this->has('grade_examiner') && $user->id !== $defense->jury_examiner_id && !$isAdmin) {
                $validator->errors()->add('grade_examiner', 'Only the jury examiner can assign this grade.');
            }

            if ($this->has('grade_supervisor') && $user->id !== $defense->jury_supervisor_id && !$isAdmin) {
                $validator->errors()->add('grade_supervisor', 'Only the jury supervisor can assign this grade.');
            }
        }
    }
}
<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Subject;
use App\Models\TeamSubjectPreference;

class TeamPreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $team = $this->route('team');
        $user = auth()->user();

        if (!$team || !$user) {
            return false;
        }

        // Only team members can manage preferences
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'preferences' => [
                'required',
                'array',
                'min:1',
                'max:5'
            ],
            'preferences.*.subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'preferences.*.preference_order' => [
                'required',
                'integer',
                'min:1',
                'max:5'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'preferences.required' => 'At least one preference is required.',
            'preferences.min' => 'At least one preference is required.',
            'preferences.max' => 'Maximum 5 preferences are allowed.',
            'preferences.*.subject_id.required' => 'Subject is required for each preference.',
            'preferences.*.subject_id.exists' => 'One or more selected subjects do not exist.',
            'preferences.*.preference_order.required' => 'Preference order is required.',
            'preferences.*.preference_order.min' => 'Preference order must be at least 1.',
            'preferences.*.preference_order.max' => 'Preference order cannot exceed 5.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('preferences')) {
            // Ensure preferences are properly indexed and sorted
            $preferences = collect($this->preferences)
                ->map(function ($preference, $index) {
                    return [
                        'subject_id' => $preference['subject_id'] ?? null,
                        'preference_order' => $preference['preference_order'] ?? ($index + 1)
                    ];
                })
                ->values()
                ->toArray();

            $this->merge(['preferences' => $preferences]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $team = $this->route('team');
            $preferences = $this->preferences ?? [];

            // Check team status
            if ($team && $team->status !== 'validated') {
                $validator->errors()->add('team', 'Only validated teams can set subject preferences.');
            }

            // Check if team is already assigned
            if ($team && $team->project) {
                $validator->errors()->add('team', 'Team is already assigned to a project and cannot change preferences.');
            }

            // Validate subject availability
            $subjectIds = collect($preferences)->pluck('subject_id')->filter();
            $availableSubjects = Subject::whereIn('id', $subjectIds)
                ->where('status', 'published')
                ->whereDoesntHave('projects')
                ->pluck('id');

            $unavailableSubjects = $subjectIds->diff($availableSubjects);
            if ($unavailableSubjects->isNotEmpty()) {
                $validator->errors()->add('preferences', 'Some selected subjects are not available for assignment.');
            }

            // Check for duplicate subjects
            $duplicateSubjects = $subjectIds->duplicates();
            if ($duplicateSubjects->isNotEmpty()) {
                $validator->errors()->add('preferences', 'Cannot have duplicate subjects in preferences.');
            }

            // Check for duplicate preference orders
            $orders = collect($preferences)->pluck('preference_order')->filter();
            $duplicateOrders = $orders->duplicates();
            if ($duplicateOrders->isNotEmpty()) {
                $validator->errors()->add('preferences', 'Cannot have duplicate preference orders.');
            }

            // Validate preference order sequence
            $sortedOrders = $orders->sort()->values();
            $expectedOrders = range(1, count($orders));
            if ($sortedOrders->toArray() !== $expectedOrders) {
                $validator->errors()->add('preferences', 'Preference orders must be sequential starting from 1.');
            }
        });
    }
}
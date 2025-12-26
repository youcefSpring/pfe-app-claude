<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResolveConflictRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $conflict = $this->route('conflict');

        return $this->user() &&
               $this->user()->role === 'department_head' &&
               $conflict->status === 'pending';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'winning_team_id' => 'required|exists:teams,id',
            'resolution_notes' => 'required|string|min:50|max:1000',
            'justification' => 'required|string|min:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'winning_team_id.required' => 'Winning team selection is required',
            'winning_team_id.exists' => 'Selected team does not exist',
            'resolution_notes.required' => 'Resolution notes are required',
            'resolution_notes.min' => 'Resolution notes must be at least 50 characters',
            'resolution_notes.max' => 'Resolution notes cannot exceed 1000 characters',
            'justification.required' => 'Justification for the decision is required',
            'justification.min' => 'Justification must be at least 100 characters',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $conflict = $this->route('conflict');
            $winningTeamId = $this->input('winning_team_id');

            if ($conflict && $winningTeamId) {
                // Check if winning team is actually part of the conflict
                $isInConflict = $conflict->teams()
                    ->where('team_id', $winningTeamId)
                    ->exists();

                if (!$isInConflict) {
                    $validator->errors()->add('winning_team_id', 'Selected team is not part of this conflict');
                }

                // Check if winning team is still eligible
                $winningTeam = \App\Models\Team::find($winningTeamId);
                if ($winningTeam) {
                    if ($winningTeam->status !== 'subject_selected') {
                        $validator->errors()->add('winning_team_id', 'Selected team is no longer eligible for assignment');
                    }

                    // Check if team's academic level matches subject
                    $subject = $conflict->subject;
                    if ($subject && $winningTeam->getAcademicLevel() !== $subject->grade) {
                        $validator->errors()->add('winning_team_id', 'Team academic level does not match subject requirements');
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
        $this->merge([
            'resolved_by' => $this->user()->id,
            'resolved_at' => now(),
            'status' => 'resolved',
        ]);
    }
}

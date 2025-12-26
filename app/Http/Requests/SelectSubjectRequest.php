<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectSubjectRequest extends FormRequest
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
               $team->status === 'complete' &&
               !$team->subject_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'motivation' => 'required|string|min:50|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'Subject selection is required',
            'subject_id.exists' => 'Selected subject does not exist',
            'motivation.required' => 'Motivation letter is required',
            'motivation.min' => 'Motivation must be at least 50 characters long',
            'motivation.max' => 'Motivation cannot exceed 1000 characters',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $team = $this->route('team');
            $subjectId = $this->input('subject_id');

            if ($subjectId) {
                $subject = \App\Models\Subject::find($subjectId);

                if ($subject) {
                    // Check if subject is validated
                    if ($subject->status !== 'validated') {
                        $validator->errors()->add('subject_id', 'Subject must be validated before selection');
                    }

                    // Check if subject matches team's academic level
                    $teamGrade = $team->getAcademicLevel();
                    if ($subject->grade !== $teamGrade) {
                        $validator->errors()->add('subject_id', 'Subject does not match team\'s academic level');
                    }

                    // Check if subject has reached maximum team limit
                    if ($subject->hasReachedMaxTeams()) {
                        $validator->errors()->add('subject_id', 'Subject has reached maximum number of teams');
                    }

                    // Check if subject is already selected by this team
                    if ($subject->isSelectedByTeam($team)) {
                        $validator->errors()->add('subject_id', 'Subject is already selected by your team');
                    }
                }
            }
        });
    }
}

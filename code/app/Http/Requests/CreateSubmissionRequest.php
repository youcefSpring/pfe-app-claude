<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user() &&
               $this->user()->role === 'student' &&
               $project->team->isMember($this->user()) &&
               $project->status === 'in_progress';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:milestone,final_report,presentation,source_code',
            'description' => 'required|string|min:50',
            'file' => 'required|file|max:51200', // 50MB max
            'version' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Submission title is required',
            'type.required' => 'Submission type is required',
            'type.in' => 'Invalid submission type',
            'description.required' => 'Description is required',
            'description.min' => 'Description must be at least 50 characters',
            'file.required' => 'File upload is required',
            'file.file' => 'Please upload a valid file',
            'file.max' => 'File size cannot exceed 50MB',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $project = $this->route('project');
            $type = $this->input('type');
            $file = $this->file('file');

            // Validate file type based on submission type
            if ($file && $type) {
                $allowedExtensions = [
                    'milestone' => ['pdf', 'doc', 'docx'],
                    'final_report' => ['pdf'],
                    'presentation' => ['pdf', 'ppt', 'pptx'],
                    'source_code' => ['zip', 'rar', '7z', 'tar', 'gz'],
                ];

                $extension = strtolower($file->getClientOriginalExtension());
                $allowed = $allowedExtensions[$type] ?? ['pdf'];

                if (!in_array($extension, $allowed)) {
                    $validator->errors()->add('file', 'Invalid file type for ' . $type . '. Allowed: ' . implode(', ', $allowed));
                }
            }

            // Check if submission type already exists and is approved
            if ($project && $type) {
                $existingSubmission = $project->submissions()
                    ->where('type', $type)
                    ->where('status', 'approved')
                    ->first();

                if ($existingSubmission) {
                    $validator->errors()->add('type', 'An approved submission of this type already exists');
                }
            }

            // Validate milestone submission timing
            if ($type === 'milestone' && $project) {
                $projectAge = $project->started_at->diffInMonths(now());
                if ($projectAge < 2) {
                    $validator->errors()->add('type', 'Milestone submissions can only be made after 2 months of project start');
                }
            }

            // Validate final report timing
            if ($type === 'final_report' && $project) {
                $projectAge = $project->started_at->diffInMonths(now());
                if ($projectAge < 4) {
                    $validator->errors()->add('type', 'Final report can only be submitted after 4 months of project start');
                }

                // Check if milestone is approved
                $milestoneApproved = $project->submissions()
                    ->where('type', 'milestone')
                    ->where('status', 'approved')
                    ->exists();

                if (!$milestoneApproved) {
                    $validator->errors()->add('type', 'Milestone must be approved before final report submission');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $project = $this->route('project');

        $this->merge([
            'project_id' => $project->id,
            'submitted_by' => $this->user()->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }
}

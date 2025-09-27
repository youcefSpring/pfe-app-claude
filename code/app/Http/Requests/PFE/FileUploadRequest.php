<?php

namespace App\Http\Requests\PFE;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,txt,jpg,jpeg,png'
            ],
            'title' => [
                'required',
                'string',
                'max:200'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'is_final_report' => [
                'boolean'
            ],
            'project_id' => [
                'required_if:type,deliverable',
                'integer',
                'exists:projects,id'
            ],
            'type' => [
                'required',
                'string',
                'in:deliverable,avatar,document'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'File is required.',
            'file.file' => 'Uploaded item must be a valid file.',
            'file.max' => 'File size cannot exceed 10MB.',
            'file.mimes' => 'File must be a PDF, Word document, text file, or image (JPG, PNG).',
            'title.required' => 'File title is required.',
            'title.max' => 'File title cannot exceed 200 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'project_id.required_if' => 'Project is required for deliverable uploads.',
            'project_id.exists' => 'Selected project does not exist.',
            'type.in' => 'Invalid file type. Must be deliverable, avatar, or document.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file')) {
                $file = $this->file('file');

                // Additional security checks
                $dangerousExtensions = ['php', 'exe', 'bat', 'sh', 'cmd', 'js', 'html'];
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, $dangerousExtensions)) {
                    $validator->errors()->add('file', 'File type is not allowed for security reasons.');
                }

                // Check file content for deliverables
                if ($this->type === 'deliverable' && !in_array($extension, ['pdf', 'doc', 'docx'])) {
                    $validator->errors()->add('file', 'Deliverables must be PDF or Word documents.');
                }

                // Check file content for avatars
                if ($this->type === 'avatar' && !in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    $validator->errors()->add('file', 'Avatar must be a JPG or PNG image.');
                }
            }

            // Validate project permissions
            if ($this->project_id && $this->type === 'deliverable') {
                $project = \App\Models\PfeProject::find($this->project_id);
                $user = auth()->user();

                if ($project && $user) {
                    $isTeamMember = $project->team->members()->where('user_id', $user->id)->exists();
                    $isSupervisor = $project->supervisor_id === $user->id;
                    $isAdmin = $user->hasRole(['admin_pfe', 'chef_master']);

                    if (!$isTeamMember && !$isSupervisor && !$isAdmin) {
                        $validator->errors()->add('project_id', 'You do not have permission to upload files for this project.');
                    }
                }
            }
        });
    }
}
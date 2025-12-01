<?php

namespace App\Http\Requests;

use App\Models\ExternalDocumentDeadline;
use Illuminate\Foundation\Http\FormRequest;

class StoreExternalDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Document name is required',
            'name.max' => 'Document name cannot exceed 255 characters',
            'file.required' => 'File upload is required',
            'file.file' => 'Please upload a valid file',
            'file.mimes' => 'Only PDF, DOC, and DOCX files are allowed',
            'file.max' => 'File size cannot exceed 10MB',
            'academic_year_id.exists' => 'Invalid academic year selected',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if upload is allowed based on deadline
            $deadline = ExternalDocumentDeadline::getActive();

            if (!$deadline) {
                $validator->errors()->add('deadline', 'No active deadline configured for external documents');
                return;
            }

            if (!$deadline->canUploadDocuments()) {
                $validator->errors()->add('deadline', 'Document upload period has ended or not yet started');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'uploaded_by' => $this->user()->id,
            'is_active' => true,
        ]);
    }
}

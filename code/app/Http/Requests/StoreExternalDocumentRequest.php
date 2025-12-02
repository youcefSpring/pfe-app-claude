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
            'name.required' => __('validation.required', ['attribute' => __('Document Name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('Document Name'), 'max' => 255]),
            'file.required' => __('validation.required', ['attribute' => __('Document File')]),
            'file.file' => __('validation.file', ['attribute' => __('Document File')]),
            'file.mimes' => __('validation.mimes', ['attribute' => __('Document File'), 'values' => 'PDF, DOC, DOCX']),
            'file.max' => __('validation.max.file', ['attribute' => __('Document File'), 'max' => 10240]),
            'academic_year_id.exists' => __('validation.exists', ['attribute' => __('Academic Year')]),
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

            // If no deadline is configured, allow the upload (admin can upload anytime)
            if (!$deadline) {
                return;
            }

            // If deadline exists but upload period has ended or not started, reject
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

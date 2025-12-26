<?php

namespace App\Http\Requests;

use App\Models\ExternalDocumentDeadline;
use App\Models\ExternalDocumentResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreExternalDocumentResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $document = $this->route('document');

        // Check if user is a student
        if (!$this->user() || $this->user()->role !== 'student') {
            return false;
        }

        // Check if user has a team
        $team = $this->user()->getTeam();
        if (!$team) {
            return false;
        }

        // Check if document is active
        if (!$document || !$document->is_active) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'File upload is required',
            'file.file' => 'Please upload a valid file',
            'file.mimes' => 'Only PDF, DOC, and DOCX files are allowed',
            'file.max' => 'File size cannot exceed 10MB',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $document = $this->route('document');
            $team = $this->user()->getTeam();

            if (!$team) {
                $validator->errors()->add('team', 'You must be part of a team to submit a response');
                return;
            }

            // Check if response period is open
            $deadline = ExternalDocumentDeadline::getActive();
            if (!$deadline) {
                $validator->errors()->add('deadline', 'No active deadline configured for external documents');
                return;
            }

            if (!$deadline->canSubmitResponses()) {
                $validator->errors()->add('deadline', 'Response submission period has ended or not yet started');
                return;
            }

            // Check if team has already submitted a response
            $existingResponse = ExternalDocumentResponse::where('external_document_id', $document->id)
                ->where('team_id', $team->id)
                ->exists();

            if ($existingResponse) {
                $validator->errors()->add('response', 'Your team has already submitted a response to this document');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $team = $this->user()->getTeam();

        if ($team) {
            $this->merge([
                'team_id' => $team->id,
                'uploaded_by' => $this->user()->id,
            ]);
        }
    }
}

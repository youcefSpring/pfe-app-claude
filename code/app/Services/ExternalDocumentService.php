<?php

namespace App\Services;

use App\Models\ExternalDocument;
use App\Models\ExternalDocumentResponse;
use App\Models\ExternalDocumentDeadline;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ExternalDocumentService
{
    /**
     * Store an external document uploaded by admin.
     */
    public function storeDocument(array $data, UploadedFile $file): ExternalDocument
    {
        // Check deadline (if configured)
        $deadline = ExternalDocumentDeadline::getActive();

        // If deadline exists but upload period has ended or not started, reject
        if ($deadline && !$deadline->canUploadDocuments()) {
            throw new \Exception('Document upload period has ended or not yet started');
        }
        // If no deadline is configured, allow the upload (admin can upload anytime)

        try {
            // Ensure directory exists and is writable
            $uploadPath = public_path('uploads/external_documents');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            // Store file in public disk
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move($uploadPath, $fileName);

            // Create document record
            $document = ExternalDocument::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'file_path' => 'uploads/external_documents/' . $fileName,
                'file_original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => $data['uploaded_by'],
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'is_active' => true,
            ]);

            return $document;
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            throw new \Exception('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Update an external document.
     */
    public function updateDocument(ExternalDocument $document, array $data, ?UploadedFile $file = null): ExternalDocument
    {
        // Update basic info
        $document->update([
            'name' => $data['name'] ?? $document->name,
            'description' => $data['description'] ?? $document->description,
        ]);

        // Update file if provided
        if ($file) {
            try {
                // Delete old file
                $oldFilePath = public_path($document->file_path);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Ensure directory exists
                $uploadPath = public_path('uploads/external_documents');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0775, true);
                }

                // Store new file
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($uploadPath, $fileName);

                $document->update([
                    'file_path' => 'uploads/external_documents/' . $fileName,
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            } catch (\Exception $e) {
                \Log::error('File update failed: ' . $e->getMessage());
                throw new \Exception('Failed to update file: ' . $e->getMessage());
            }
        }

        return $document->fresh();
    }

    /**
     * Delete an external document.
     */
    public function deleteDocument(ExternalDocument $document): bool
    {
        // Delete file
        $filePath = public_path($document->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete all responses and their files
        foreach ($document->responses as $response) {
            $this->deleteResponse($response);
        }

        // Delete document
        return $document->delete();
    }

    /**
     * Store a team response to an external document.
     */
    public function storeResponse(ExternalDocument $document, array $data, UploadedFile $file): ExternalDocumentResponse
    {
        // Check deadline
        $deadline = ExternalDocumentDeadline::getActive();
        if (!$deadline || !$deadline->canSubmitResponses()) {
            throw new \Exception('Response submission period has ended or not yet started');
        }

        // Check if team has already submitted
        $existing = ExternalDocumentResponse::where('external_document_id', $document->id)
            ->where('team_id', $data['team_id'])
            ->first();

        if ($existing) {
            throw new \Exception('Your team has already submitted a response to this document');
        }

        try {
            // Ensure directory exists and is writable
            $uploadPath = public_path('uploads/external_document_responses');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            // Store file in public disk
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move($uploadPath, $fileName);

            // Create response record
            $response = ExternalDocumentResponse::create([
                'external_document_id' => $document->id,
                'team_id' => $data['team_id'],
                'file_path' => 'uploads/external_document_responses/' . $fileName,
                'file_original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => $data['uploaded_by'],
            ]);

            return $response;
        } catch (\Exception $e) {
            \Log::error('Response upload failed: ' . $e->getMessage());
            throw new \Exception('Failed to upload response: ' . $e->getMessage());
        }
    }

    /**
     * Add admin feedback to a response.
     */
    public function addFeedback(ExternalDocumentResponse $response, array $data): ExternalDocumentResponse
    {
        $response->update([
            'admin_feedback' => $data['admin_feedback'],
            'feedback_by' => $data['feedback_by'],
            'feedback_at' => $data['feedback_at'],
        ]);

        return $response->fresh();
    }

    /**
     * Delete a response.
     */
    public function deleteResponse(ExternalDocumentResponse $response): bool
    {
        // Delete file
        $filePath = public_path($response->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete response
        return $response->delete();
    }

    /**
     * Get all active documents.
     */
    public function getActiveDocuments(?int $academicYearId = null)
    {
        $query = ExternalDocument::with(['uploader', 'academicYear'])
            ->where('is_active', true);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        return $query->latest()->get();
    }

    /**
     * Get documents with response status for a team.
     */
    public function getDocumentsForTeam(Team $team)
    {
        $documents = $this->getActiveDocuments();

        return $documents->map(function ($document) use ($team) {
            $response = ExternalDocumentResponse::where('external_document_id', $document->id)
                ->where('team_id', $team->id)
                ->first();

            $document->team_response = $response;
            $document->has_responded = !is_null($response);

            return $document;
        });
    }

    /**
     * Get document with all responses (admin view).
     */
    public function getDocumentWithResponses(ExternalDocument $document)
    {
        return $document->load([
            'responses.team.members.student',
            'responses.uploader',
            'responses.feedbackProvider'
        ]);
    }

    /**
     * Toggle document active status.
     */
    public function toggleActive(ExternalDocument $document): ExternalDocument
    {
        $document->update(['is_active' => !$document->is_active]);
        return $document->fresh();
    }

    /**
     * Download document file.
     */
    public function downloadDocument(ExternalDocument $document)
    {
        $filePath = public_path($document->file_path);

        if (!file_exists($filePath)) {
            throw new \Exception('File not found');
        }

        return response()->download($filePath, $document->file_original_name);
    }

    /**
     * Download response file.
     */
    public function downloadResponse(ExternalDocumentResponse $response)
    {
        $filePath = public_path($response->file_path);

        if (!file_exists($filePath)) {
            throw new \Exception('File not found');
        }

        return response()->download($filePath, $response->file_original_name);
    }
}

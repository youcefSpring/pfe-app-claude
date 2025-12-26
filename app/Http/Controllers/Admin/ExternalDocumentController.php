<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalDocument;
use App\Models\ExternalDocumentResponse;
use App\Models\AcademicYear;
use App\Services\ExternalDocumentService;
use App\Http\Requests\StoreExternalDocumentRequest;
use App\Http\Requests\UpdateExternalDocumentFeedbackRequest;
use Illuminate\Http\Request;

class ExternalDocumentController extends Controller
{
    protected $externalDocumentService;

    public function __construct(ExternalDocumentService $externalDocumentService)
    {
        $this->externalDocumentService = $externalDocumentService;
    }

    /**
     * Display a listing of external documents.
     */
    public function index()
    {
        $documents = ExternalDocument::with(['uploader', 'academicYear', 'responses'])
            ->latest()
            ->paginate(15);

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        return view('admin.external_documents.index', compact('documents', 'academicYears'));
    }

    /**
     * Show the form for creating a new external document.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        return view('admin.external_documents.create', compact('academicYears'));
    }

    /**
     * Store a newly created external document.
     */
    public function store(StoreExternalDocumentRequest $request)
    {
        try {
            $document = $this->externalDocumentService->storeDocument(
                $request->validated(),
                $request->file('file')
            );

            return redirect()
                ->route('admin.external-documents.index')
                ->with('success', __('messages.document_uploaded_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified external document with all responses.
     */
    public function show(ExternalDocument $externalDocument)
    {
        $document = $this->externalDocumentService->getDocumentWithResponses($externalDocument);

        return view('admin.external_documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified external document.
     */
    public function edit(ExternalDocument $externalDocument)
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        return view('admin.external_documents.edit', compact('externalDocument', 'academicYears'));
    }

    /**
     * Update the specified external document.
     */
    public function update(Request $request, ExternalDocument $externalDocument)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            $this->externalDocumentService->updateDocument(
                $externalDocument,
                $request->all(),
                $request->file('file')
            );

            return redirect()
                ->route('admin.external-documents.index')
                ->with('success', __('messages.document_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified external document.
     */
    public function destroy(ExternalDocument $externalDocument)
    {
        try {
            $this->externalDocumentService->deleteDocument($externalDocument);

            return redirect()
                ->route('admin.external-documents.index')
                ->with('success', __('messages.document_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Download the external document file.
     */
    public function download(ExternalDocument $externalDocument)
    {
        try {
            return $this->externalDocumentService->downloadDocument($externalDocument);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle document active status.
     */
    public function toggleActive(ExternalDocument $externalDocument)
    {
        try {
            $this->externalDocumentService->toggleActive($externalDocument);

            $status = $externalDocument->is_active ? 'deactivated' : 'activated';
            return redirect()
                ->back()
                ->with('success', __('messages.document_' . $status));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Download a team response file.
     */
    public function downloadResponse(ExternalDocumentResponse $response)
    {
        try {
            return $this->externalDocumentService->downloadResponse($response);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show form to add feedback to a response.
     */
    public function showFeedbackForm(ExternalDocumentResponse $response)
    {
        return view('admin.external_documents.feedback', compact('response'));
    }

    /**
     * Store feedback for a team response.
     */
    public function storeFeedback(UpdateExternalDocumentFeedbackRequest $request, ExternalDocumentResponse $response)
    {
        try {
            $this->externalDocumentService->addFeedback($response, $request->validated());

            return redirect()
                ->route('admin.external-documents.show', $response->external_document_id)
                ->with('success', __('messages.feedback_added_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}

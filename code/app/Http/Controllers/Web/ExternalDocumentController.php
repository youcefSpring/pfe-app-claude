<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExternalDocument;
use App\Models\ExternalDocumentDeadline;
use App\Services\ExternalDocumentService;
use App\Http\Requests\StoreExternalDocumentResponseRequest;
use Illuminate\Http\Request;

class ExternalDocumentController extends Controller
{
    protected $externalDocumentService;

    public function __construct(ExternalDocumentService $externalDocumentService)
    {
        $this->externalDocumentService = $externalDocumentService;
    }

    /**
     * Display a listing of external documents for the team.
     */
    public function index()
    {
        // Check if user is a student
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        // Get user's team
        $team = auth()->user()->getTeam();

        if (!$team) {
            return view('web.external_documents.no_team');
        }

        // Get active deadline info
        $deadline = ExternalDocumentDeadline::getActive();

        // Get documents with response status for the team
        $documents = $this->externalDocumentService->getDocumentsForTeam($team);

        return view('web.external_documents.index', compact('documents', 'team', 'deadline'));
    }

    /**
     * Display the specified external document.
     */
    public function show(ExternalDocument $externalDocument)
    {
        // Check if user is a student
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        // Check if document is active
        if (!$externalDocument->is_active) {
            abort(404, 'Document not found');
        }

        // Get user's team
        $team = auth()->user()->getTeam();

        if (!$team) {
            return redirect()->route('external-documents.index')->with('error', __('messages.must_have_team'));
        }

        // Check if team has already responded
        $response = $externalDocument->responses()->where('team_id', $team->id)->first();

        // Get active deadline
        $deadline = ExternalDocumentDeadline::getActive();

        return view('web.external_documents.show', compact('externalDocument', 'team', 'response', 'deadline'));
    }

    /**
     * Store a team response to an external document.
     */
    public function storeResponse(StoreExternalDocumentResponseRequest $request, ExternalDocument $externalDocument)
    {
        try {
            $response = $this->externalDocumentService->storeResponse(
                $externalDocument,
                $request->validated(),
                $request->file('file')
            );

            // Notify admin (will be implemented in notification system)
            // event(new TeamRespondedToDocument($response));

            return redirect()
                ->route('external-documents.show', $externalDocument)
                ->with('success', __('messages.response_submitted_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Download the external document file.
     */
    public function download(ExternalDocument $externalDocument)
    {
        // Check if user is a student
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        // Check if document is active
        if (!$externalDocument->is_active) {
            abort(404, 'Document not found');
        }

        try {
            return $this->externalDocumentService->downloadDocument($externalDocument);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * View team's own response (with feedback if available).
     */
    public function viewResponse(ExternalDocument $externalDocument)
    {
        // Check if user is a student
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized');
        }

        // Get user's team
        $team = auth()->user()->getTeam();

        if (!$team) {
            return redirect()->route('external-documents.index')->with('error', __('messages.must_have_team'));
        }

        // Get team's response
        $response = $externalDocument->responses()
            ->where('team_id', $team->id)
            ->with(['feedbackProvider'])
            ->first();

        if (!$response) {
            abort(404, 'Response not found');
        }

        return view('web.external_documents.view_response', compact('externalDocument', 'response', 'team'));
    }
}

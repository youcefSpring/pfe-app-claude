<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Defense;
use App\Models\PfeProject;
use App\Models\TeamMember;
use App\Models\DefensePreparation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class StudentDefenseController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Defense overview dashboard
     */
    public function index(): View
    {
        $student = auth()->user();
        $project = $this->getCurrentProject($student);

        if (!$project) {
            return view('pfe.student.defense.no-project');
        }

        $defense = $project->defense;
        $preparationStatus = $this->getPreparationStatus($project);
        $defenseRequirements = $this->getDefenseRequirements();

        return view('pfe.student.defense.index', [
            'project' => $project,
            'defense' => $defense,
            'preparation_status' => $preparationStatus,
            'requirements' => $defenseRequirements
        ]);
    }

    /**
     * Defense preparation checklist and tools
     */
    public function preparation(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $defense = $project->defense;
        $checklist = $this->getPreparationChecklist($project);
        $preparationData = $this->getPreparationData($project);
        $guidelines = $this->getDefenseGuidelines();
        $resources = $this->getPreparationResources();

        return view('pfe.student.defense.preparation', [
            'project' => $project,
            'defense' => $defense,
            'checklist' => $checklist,
            'preparation_data' => $preparationData,
            'guidelines' => $guidelines,
            'resources' => $resources
        ]);
    }

    /**
     * Update preparation checklist
     */
    public function updatePreparation(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('manage', $project);

        $request->validate([
            'checklist_items' => 'required|array',
            'checklist_items.*' => 'boolean',
            'presentation_notes' => 'nullable|string|max:2000',
            'demo_notes' => 'nullable|string|max:2000',
            'qa_preparation' => 'nullable|string|max:2000',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        $preparation = DefensePreparation::updateOrCreate(
            ['project_id' => $project->id],
            [
                'checklist_items' => $request->checklist_items,
                'presentation_notes' => $request->presentation_notes,
                'demo_notes' => $request->demo_notes,
                'qa_preparation' => $request->qa_preparation,
                'additional_notes' => $request->additional_notes,
                'last_updated_by' => auth()->id(),
                'completion_percentage' => $this->calculateCompletionPercentage($request->checklist_items)
            ]
        );

        return back()->with('success', 'Preparation progress updated successfully!');
    }

    /**
     * Upload presentation materials
     */
    public function uploadPresentation(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('manage', $project);

        $request->validate([
            'presentation_file' => 'required|file|mimes:pdf,ppt,pptx|max:20480', // 20MB
            'presentation_title' => 'required|string|max:200',
            'presentation_description' => 'nullable|string|max:500',
            'version' => 'required|string|max:50'
        ]);

        // Store presentation file
        $filePath = $request->file('presentation_file')->store(
            "projects/{$project->id}/presentations",
            'local'
        );

        // Create presentation record
        $presentation = $project->presentations()->create([
            'title' => $request->presentation_title,
            'description' => $request->presentation_description,
            'version' => $request->version,
            'file_path' => $filePath,
            'original_filename' => $request->file('presentation_file')->getClientOriginalName(),
            'file_size' => $request->file('presentation_file')->getSize(),
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
            'is_final' => false
        ]);

        // Notify supervisor
        $this->notificationService->notify(
            $project->supervisor,
            'presentation_uploaded',
            'Defense presentation uploaded',
            "Team {$project->team->name} uploaded their defense presentation",
            ['presentation_id' => $presentation->id, 'project_id' => $project->id]
        );

        return back()->with('success', 'Presentation uploaded successfully!');
    }

    /**
     * Upload demo materials
     */
    public function uploadDemo(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('manage', $project);

        $request->validate([
            'demo_file' => 'required|file|max:102400', // 100MB for demo files
            'demo_title' => 'required|string|max:200',
            'demo_description' => 'required|string|max:1000',
            'demo_type' => 'required|in:video,software,documentation,other',
            'setup_instructions' => 'nullable|string|max:2000'
        ]);

        // Store demo file
        $filePath = $request->file('demo_file')->store(
            "projects/{$project->id}/demos",
            'local'
        );

        // Create demo record
        $demo = $project->demos()->create([
            'title' => $request->demo_title,
            'description' => $request->demo_description,
            'type' => $request->demo_type,
            'file_path' => $filePath,
            'original_filename' => $request->file('demo_file')->getClientOriginalName(),
            'file_size' => $request->file('demo_file')->getSize(),
            'setup_instructions' => $request->setup_instructions,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now()
        ]);

        return back()->with('success', 'Demo materials uploaded successfully!');
    }

    /**
     * Practice session scheduler
     */
    public function schedule练习(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('manage', $project);

        $request->validate([
            'practice_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:30|max:180',
            'location' => 'nullable|string|max:200',
            'agenda' => 'required|string|max:1000',
            'attendees' => 'required|array|min:1',
            'attendees.*' => 'exists:users,id'
        ]);

        $practiceSession = $project->practiceSessions()->create([
            'scheduled_at' => $request->practice_date,
            'duration_minutes' => $request->duration_minutes,
            'location' => $request->location,
            'agenda' => $request->agenda,
            'organized_by' => auth()->id(),
            'status' => 'scheduled'
        ]);

        // Notify attendees
        foreach ($request->attendees as $attendeeId) {
            $attendee = \App\Models\User::find($attendeeId);
            if ($attendee) {
                $this->notificationService->notify(
                    $attendee,
                    'practice_session_scheduled',
                    'Defense practice session scheduled',
                    "Practice session scheduled for " . \Carbon\Carbon::parse($request->practice_date)->format('M d, Y \a\t H:i'),
                    ['practice_session_id' => $practiceSession->id, 'project_id' => $project->id]
                );
            }
        }

        return back()->with('success', 'Practice session scheduled successfully!');
    }

    /**
     * Defense readiness assessment
     */
    public function readinessAssessment(PfeProject $project): View
    {
        $this->authorize('view', $project);

        $assessment = $this->performReadinessAssessment($project);
        $recommendations = $this->getReadinessRecommendations($assessment);
        $checklist = $this->getFinalChecklist($project);

        return view('pfe.student.defense.assessment', [
            'project' => $project,
            'assessment' => $assessment,
            'recommendations' => $recommendations,
            'checklist' => $checklist
        ]);
    }

    /**
     * Submit defense readiness
     */
    public function submitReadiness(Request $request, PfeProject $project): RedirectResponse
    {
        $this->authorize('manage', $project);

        $request->validate([
            'team_confirmation' => 'required|accepted',
            'final_checklist' => 'required|array',
            'final_checklist.*' => 'accepted',
            'readiness_notes' => 'nullable|string|max:1000'
        ]);

        // Update project status
        $project->update([
            'status' => 'ready_for_defense',
            'defense_readiness_submitted_at' => now(),
            'defense_readiness_submitted_by' => auth()->id(),
            'readiness_notes' => $request->readiness_notes
        ]);

        // Notify supervisor and admin
        $this->notificationService->notify(
            $project->supervisor,
            'defense_readiness_submitted',
            'Team ready for defense',
            "Team {$project->team->name} has confirmed readiness for defense",
            ['project_id' => $project->id]
        );

        // Notify admin for defense scheduling
        $admin = \App\Models\User::role('admin_pfe')->first();
        if ($admin) {
            $this->notificationService->notify(
                $admin,
                'defense_scheduling_request',
                'Defense scheduling request',
                "Team {$project->team->name} is ready for defense scheduling",
                ['project_id' => $project->id]
            );
        }

        return redirect()->route('pfe.student.defense.index')
            ->with('success', 'Defense readiness submitted! Your defense will be scheduled soon.');
    }

    /**
     * View defense details (when scheduled)
     */
    public function viewDefense(Defense $defense): View
    {
        $student = auth()->user();
        $project = $defense->project;

        $this->authorize('view', $project);

        $defenseInfo = $this->getDefenseInfo($defense);
        $juryInfo = $this->getJuryInfo($defense);
        $logistics = $this->getDefenseLogistics($defense);
        $finalPreparation = $this->getFinalPreparationTips();

        return view('pfe.student.defense.show', [
            'defense' => $defense,
            'project' => $project,
            'defense_info' => $defenseInfo,
            'jury_info' => $juryInfo,
            'logistics' => $logistics,
            'final_preparation' => $finalPreparation
        ]);
    }

    /**
     * Get current project for student
     */
    private function getCurrentProject($student): ?PfeProject
    {
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->with('team.project')
            ->first();

        return $teamMember?->team?->project;
    }

    /**
     * Get preparation status
     */
    private function getPreparationStatus(PfeProject $project): array
    {
        $preparation = DefensePreparation::where('project_id', $project->id)->first();
        $defense = $project->defense;

        $status = [
            'overall_readiness' => 0,
            'preparation_complete' => false,
            'materials_uploaded' => false,
            'practice_completed' => false,
            'team_ready' => false
        ];

        if ($preparation) {
            $status['overall_readiness'] = $preparation->completion_percentage ?? 0;
            $status['preparation_complete'] = $preparation->completion_percentage >= 80;
        }

        $status['materials_uploaded'] = $project->presentations()->exists() ||
                                       $project->demos()->exists();

        $status['practice_completed'] = $project->practiceSessions()
            ->where('status', 'completed')
            ->exists();

        $status['team_ready'] = $project->status === 'ready_for_defense';

        return $status;
    }

    /**
     * Get defense requirements
     */
    private function getDefenseRequirements(): array
    {
        return [
            'duration' => '30-45 minutes',
            'presentation_time' => '15-20 minutes',
            'demo_time' => '10-15 minutes',
            'qa_time' => '10-15 minutes',
            'required_materials' => [
                'Final project report (PDF)',
                'Presentation slides (PPT/PDF)',
                'Working demo/prototype',
                'Source code (if applicable)',
                'User manual/documentation'
            ],
            'evaluation_criteria' => [
                'Technical implementation (30%)',
                'Innovation and creativity (20%)',
                'Presentation quality (20%)',
                'Problem solving approach (15%)',
                'Documentation quality (15%)'
            ]
        ];
    }

    /**
     * Get preparation checklist
     */
    private function getPreparationChecklist(PfeProject $project): array
    {
        return [
            'documentation' => [
                'final_report_completed' => 'Final project report completed and reviewed',
                'user_manual_prepared' => 'User manual/documentation prepared',
                'technical_specs_documented' => 'Technical specifications documented',
                'references_cited' => 'All references properly cited'
            ],
            'presentation' => [
                'slides_prepared' => 'Presentation slides prepared (15-20 slides)',
                'timing_practiced' => 'Presentation timing practiced (15-20 minutes)',
                'visual_aids_ready' => 'Visual aids and graphics prepared',
                'speaker_notes_prepared' => 'Speaker notes prepared'
            ],
            'demonstration' => [
                'demo_working' => 'Live demonstration tested and working',
                'backup_plan' => 'Backup plan prepared (video/screenshots)',
                'test_data_prepared' => 'Test data and scenarios prepared',
                'equipment_tested' => 'Equipment and setup tested'
            ],
            'qa_preparation' => [
                'common_questions_prepared' => 'Common questions identified and practiced',
                'technical_details_reviewed' => 'Technical details thoroughly reviewed',
                'limitations_identified' => 'Project limitations and challenges identified',
                'future_work_discussed' => 'Future work and improvements discussed'
            ],
            'logistics' => [
                'room_location_confirmed' => 'Defense room location confirmed',
                'equipment_requirements_checked' => 'Equipment requirements checked',
                'materials_organized' => 'All materials organized and ready',
                'team_roles_assigned' => 'Team presentation roles assigned'
            ]
        ];
    }

    /**
     * Calculate completion percentage
     */
    private function calculateCompletionPercentage(array $checklistItems): int
    {
        $total = count($checklistItems);
        $completed = count(array_filter($checklistItems));

        return $total > 0 ? intval(($completed / $total) * 100) : 0;
    }

    /**
     * Perform readiness assessment
     */
    private function performReadinessAssessment(PfeProject $project): array
    {
        $scores = [];

        // Documentation completeness
        $documentationScore = 0;
        if ($project->final_report_path) $documentationScore += 40;
        if ($project->deliverables()->where('status', 'approved')->count() >= 3) $documentationScore += 35;
        if ($project->demos()->exists()) $documentationScore += 25;
        $scores['documentation'] = min(100, $documentationScore);

        // Presentation readiness
        $presentationScore = 0;
        if ($project->presentations()->exists()) $presentationScore += 50;
        if ($project->practiceSessions()->where('status', 'completed')->exists()) $presentationScore += 30;
        $preparationData = DefensePreparation::where('project_id', $project->id)->first();
        if ($preparationData && $preparationData->completion_percentage >= 80) $presentationScore += 20;
        $scores['presentation'] = min(100, $presentationScore);

        // Technical readiness
        $technicalScore = 0;
        $approvedDeliverables = $project->deliverables()->where('status', 'approved')->count();
        $technicalScore += min(60, $approvedDeliverables * 15);
        if ($project->demos()->exists()) $technicalScore += 40;
        $scores['technical'] = min(100, $technicalScore);

        // Overall readiness
        $scores['overall'] = intval(array_sum($scores) / count($scores));

        return $scores;
    }

    /**
     * Get readiness recommendations
     */
    private function getReadinessRecommendations(array $assessment): array
    {
        $recommendations = [];

        if ($assessment['documentation'] < 80) {
            $recommendations[] = [
                'type' => 'warning',
                'area' => 'Documentation',
                'message' => 'Complete all required documentation including final report and user manual.',
                'priority' => 'high'
            ];
        }

        if ($assessment['presentation'] < 70) {
            $recommendations[] = [
                'type' => 'warning',
                'area' => 'Presentation',
                'message' => 'Practice your presentation and ensure all materials are ready.',
                'priority' => 'high'
            ];
        }

        if ($assessment['technical'] < 75) {
            $recommendations[] = [
                'type' => 'warning',
                'area' => 'Technical',
                'message' => 'Test your demonstration thoroughly and prepare backup options.',
                'priority' => 'medium'
            ];
        }

        if ($assessment['overall'] >= 85) {
            $recommendations[] = [
                'type' => 'success',
                'area' => 'Overall',
                'message' => 'Great job! You are well prepared for your defense.',
                'priority' => 'info'
            ];
        }

        return $recommendations;
    }

    // Additional helper methods
    private function getPreparationData($project): array { return []; }
    private function getDefenseGuidelines(): array { return []; }
    private function getPreparationResources(): array { return []; }
    private function getFinalChecklist($project): array { return []; }
    private function getDefenseInfo($defense): array { return []; }
    private function getJuryInfo($defense): array { return []; }
    private function getDefenseLogistics($defense): array { return []; }
    private function getFinalPreparationTips(): array { return []; }
}
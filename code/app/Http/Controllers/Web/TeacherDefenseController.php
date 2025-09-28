<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Defense;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PvGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherDefenseController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private PvGenerationService $pvService
    ) {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    /**
     * Teacher defense dashboard
     */
    public function index(Request $request): View
    {
        $teacher = auth()->user();

        // Get defenses where teacher is involved as jury member
        $query = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->with([
            'project.team.members.user',
            'project.subject',
            'room',
            'juryPresident',
            'juryExaminer',
            'jurySupervisor'
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('defense_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('defense_date', '<=', $request->date_to);
        }

        $defenses = $query->orderBy('defense_date', 'desc')->paginate(15);

        $upcomingDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->where('defense_date', '>=', now())
        ->orderBy('defense_date')
        ->take(5)
        ->get();

        $stats = $this->getDefenseStats($teacher);

        return view('pfe.teacher.defenses.index', [
            'defenses' => $defenses,
            'upcoming_defenses' => $upcomingDefenses,
            'stats' => $stats,
            'filters' => $request->only(['status', 'date_from', 'date_to'])
        ]);
    }

    /**
     * Show defense evaluation form
     */
    public function showEvaluation(Defense $defense): View
    {
        $this->authorize('evaluate', $defense);

        $defense->load([
            'project.team.members.user',
            'project.subject',
            'project.deliverables' => function($query) {
                $query->where('status', 'approved')->orderBy('submitted_at', 'desc');
            },
            'evaluations' => function($query) {
                $query->where('evaluator_id', auth()->id());
            }
        ]);

        $evaluationCriteria = $this->getEvaluationCriteria();
        $myRole = $this->getJuryRole($defense, auth()->user());
        $existingEvaluation = $defense->evaluations()
            ->where('evaluator_id', auth()->id())
            ->first();

        return view('pfe.teacher.defenses.evaluation', [
            'defense' => $defense,
            'evaluation_criteria' => $evaluationCriteria,
            'my_role' => $myRole,
            'existing_evaluation' => $existingEvaluation
        ]);
    }

    /**
     * Submit defense evaluation
     */
    public function submitEvaluation(Request $request, Defense $defense): RedirectResponse
    {
        $this->authorize('evaluate', $defense);

        $request->validate([
            'presentation_score' => 'required|numeric|min:0|max:20',
            'technical_score' => 'required|numeric|min:0|max:20',
            'report_score' => 'required|numeric|min:0|max:20',
            'questions_score' => 'required|numeric|min:0|max:20',
            'overall_comments' => 'nullable|string|max:2000',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'criteria_scores' => 'nullable|array',
            'criteria_scores.*' => 'numeric|min:0|max:5'
        ]);

        $finalScore = ($request->presentation_score + $request->technical_score +
                      $request->report_score + $request->questions_score) / 4;

        // Create or update evaluation
        $evaluation = $defense->evaluations()->updateOrCreate(
            ['evaluator_id' => auth()->id()],
            [
                'presentation_score' => $request->presentation_score,
                'technical_score' => $request->technical_score,
                'report_score' => $request->report_score,
                'questions_score' => $request->questions_score,
                'final_score' => $finalScore,
                'overall_comments' => $request->overall_comments,
                'strengths' => $request->strengths,
                'weaknesses' => $request->weaknesses,
                'recommendations' => $request->recommendations,
                'criteria_scores' => $request->criteria_scores,
                'jury_role' => $this->getJuryRole($defense, auth()->user()),
                'evaluated_at' => now()
            ]
        );

        // Check if all jury members have evaluated
        $this->checkEvaluationCompletion($defense);

        return redirect()->route('pfe.teacher.defenses.index')
            ->with('success', 'Evaluation submitted successfully');
    }

    /**
     * Defense preparation checklist
     */
    public function showPreparation(Defense $defense): View
    {
        $this->authorize('participate', $defense);

        $defense->load([
            'project.team.members.user',
            'project.subject',
            'project.deliverables',
            'room'
        ]);

        $checklist = $this->getPreparationChecklist($defense);
        $myRole = $this->getJuryRole($defense, auth()->user());
        $documents = $this->getDefenseDocuments($defense);

        return view('pfe.teacher.defenses.preparation', [
            'defense' => $defense,
            'checklist' => $checklist,
            'my_role' => $myRole,
            'documents' => $documents
        ]);
    }

    /**
     * Mark attendance for defense
     */
    public function markAttendance(Request $request, Defense $defense): RedirectResponse
    {
        $this->authorize('participate', $defense);

        $request->validate([
            'attendance_status' => 'required|in:present,absent,late',
            'arrival_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);

        $attendance = $defense->attendances()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'status' => $request->attendance_status,
                'arrival_time' => $request->arrival_time,
                'notes' => $request->notes,
                'marked_at' => now()
            ]
        );

        return back()->with('success', 'Attendance marked successfully');
    }

    /**
     * View defense calendar
     */
    public function calendar(Request $request): View
    {
        $teacher = auth()->user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $defenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->whereBetween('defense_date', [$startDate, $endDate])
        ->with(['project.team', 'project.subject', 'room'])
        ->get();

        return view('pfe.teacher.defenses.calendar', [
            'defenses' => $defenses,
            'current_month' => $startDate,
            'calendar_data' => $this->generateCalendarData($defenses, $startDate)
        ]);
    }

    /**
     * Export defense schedule
     */
    public function exportSchedule(Request $request)
    {
        $teacher = auth()->user();
        $format = $request->get('format', 'pdf');

        $defenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })
        ->with(['project.team', 'project.subject', 'room'])
        ->orderBy('defense_date')
        ->get();

        if ($format === 'ical') {
            return $this->exportToIcal($defenses, $teacher);
        }

        return $this->exportToPdf($defenses, $teacher);
    }

    /**
     * Submit final defense decision (for jury president)
     */
    public function submitFinalDecision(Request $request, Defense $defense): RedirectResponse
    {
        $this->authorize('finalize', $defense);

        if ($defense->jury_president_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Only the jury president can submit final decisions']);
        }

        $request->validate([
            'final_grade' => 'required|numeric|min:0|max:20',
            'final_decision' => 'required|in:passed,failed,conditional_pass',
            'final_comments' => 'nullable|string|max:2000',
            'deliberation_notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($request, $defense) {
            // Update defense with final decision
            $defense->update([
                'final_grade' => $request->final_grade,
                'final_decision' => $request->final_decision,
                'final_comments' => $request->final_comments,
                'deliberation_notes' => $request->deliberation_notes,
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Update project status
            $projectStatus = $request->final_decision === 'passed' ? 'completed' : 'failed';
            $defense->project->update(['status' => $projectStatus]);

            // Generate PV if passed
            if ($request->final_decision === 'passed') {
                try {
                    $this->pvService->generateDefensePv($defense);
                } catch (\Exception $e) {
                    // Log error but don't fail the transaction
                    logger()->error('PV generation failed: ' . $e->getMessage());
                }
            }

            // Notify all participants
            $this->notifyDefenseCompletion($defense);
        });

        return redirect()->route('pfe.teacher.defenses.index')
            ->with('success', 'Final decision submitted successfully');
    }

    /**
     * Get defense statistics for teacher
     */
    private function getDefenseStats($teacher): array
    {
        $totalDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->count();

        $completedDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->where('status', 'completed')->count();

        $upcomingDefenses = Defense::where(function($q) use ($teacher) {
            $q->where('jury_president_id', $teacher->id)
              ->orWhere('jury_examiner_id', $teacher->id)
              ->orWhere('jury_supervisor_id', $teacher->id);
        })->where('defense_date', '>=', now())->count();

        $presidedDefenses = Defense::where('jury_president_id', $teacher->id)->count();

        return [
            'total_defenses' => $totalDefenses,
            'completed_defenses' => $completedDefenses,
            'upcoming_defenses' => $upcomingDefenses,
            'presided_defenses' => $presidedDefenses,
            'completion_rate' => $totalDefenses > 0 ? ($completedDefenses / $totalDefenses) * 100 : 0,
            'average_grade' => $this->getAverageGradeGiven($teacher)
        ];
    }

    /**
     * Get evaluation criteria
     */
    private function getEvaluationCriteria(): array
    {
        return [
            'presentation' => [
                'clarity' => 'Clarity and organization of presentation',
                'time_management' => 'Effective time management',
                'communication' => 'Communication skills',
                'visual_aids' => 'Quality of visual aids and materials'
            ],
            'technical' => [
                'technical_depth' => 'Technical depth and understanding',
                'innovation' => 'Innovation and creativity',
                'implementation' => 'Quality of implementation',
                'methodology' => 'Appropriateness of methodology'
            ],
            'report' => [
                'structure' => 'Document structure and organization',
                'content' => 'Content quality and completeness',
                'references' => 'Quality of references and citations',
                'writing' => 'Writing quality and clarity'
            ],
            'defense' => [
                'question_handling' => 'Handling of questions',
                'knowledge_depth' => 'Depth of knowledge demonstrated',
                'critical_thinking' => 'Critical thinking and analysis',
                'professionalism' => 'Professional demeanor'
            ]
        ];
    }

    /**
     * Get jury role for user in defense
     */
    private function getJuryRole(Defense $defense, User $user): string
    {
        if ($defense->jury_president_id === $user->id) {
            return 'president';
        } elseif ($defense->jury_examiner_id === $user->id) {
            return 'examiner';
        } elseif ($defense->jury_supervisor_id === $user->id) {
            return 'supervisor';
        }
        return 'observer';
    }

    /**
     * Check if all evaluations are complete
     */
    private function checkEvaluationCompletion(Defense $defense): void
    {
        $requiredEvaluators = collect([
            $defense->jury_president_id,
            $defense->jury_examiner_id,
            $defense->jury_supervisor_id
        ])->filter();

        $completedEvaluations = $defense->evaluations()
            ->whereIn('evaluator_id', $requiredEvaluators)
            ->count();

        if ($completedEvaluations === $requiredEvaluators->count()) {
            $defense->update(['evaluation_status' => 'completed']);

            // Notify jury president to finalize
            if ($defense->jury_president_id !== auth()->id()) {
                $this->notificationService->notify(
                    $defense->juryPresident,
                    'evaluation_completed',
                    'All evaluations completed',
                    'All jury members have completed their evaluations. Please submit final decision.',
                    ['defense_id' => $defense->id]
                );
            }
        }
    }

    /**
     * Get preparation checklist
     */
    private function getPreparationChecklist(Defense $defense): array
    {
        $role = $this->getJuryRole($defense, auth()->user());

        $baseChecklist = [
            'review_project_deliverables' => 'Review all project deliverables',
            'read_project_report' => 'Read the final project report thoroughly',
            'prepare_questions' => 'Prepare relevant questions for the defense',
            'review_evaluation_criteria' => 'Review evaluation criteria and grading rubric'
        ];

        $roleSpecificItems = [
            'president' => [
                'prepare_opening_remarks' => 'Prepare opening remarks',
                'plan_session_structure' => 'Plan defense session structure',
                'review_deliberation_process' => 'Review deliberation process'
            ],
            'examiner' => [
                'prepare_technical_questions' => 'Prepare technical questions',
                'review_similar_projects' => 'Review similar projects for comparison'
            ],
            'supervisor' => [
                'prepare_supervision_summary' => 'Prepare supervision summary',
                'review_student_progress' => 'Review student progress throughout project'
            ]
        ];

        return array_merge($baseChecklist, $roleSpecificItems[$role] ?? []);
    }

    /**
     * Get defense documents
     */
    private function getDefenseDocuments(Defense $defense): array
    {
        return [
            'project_report' => $defense->project->final_report_path,
            'deliverables' => $defense->project->deliverables()
                ->where('status', 'approved')
                ->select('title', 'file_path', 'submitted_at')
                ->get()
                ->toArray(),
            'evaluation_sheets' => $this->getEvaluationSheets($defense),
            'defense_schedule' => $this->getDefenseScheduleDocument($defense)
        ];
    }

    /**
     * Generate calendar data
     */
    private function generateCalendarData($defenses, $startDate): array
    {
        $calendarData = [];
        $daysInMonth = $startDate->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = $startDate->copy()->day($day);
            $dayDefenses = $defenses->filter(function($defense) use ($currentDate) {
                return $defense->defense_date->isSameDay($currentDate);
            });

            $calendarData[$day] = [
                'date' => $currentDate,
                'defenses' => $dayDefenses,
                'count' => $dayDefenses->count()
            ];
        }

        return $calendarData;
    }

    /**
     * Notify defense completion
     */
    private function notifyDefenseCompletion(Defense $defense): void
    {
        // Notify team members
        foreach ($defense->project->team->members as $member) {
            $this->notificationService->notify(
                $member->user,
                'defense_completed',
                'Defense completed',
                "Defense result: {$defense->final_decision}",
                ['defense_id' => $defense->id]
            );
        }

        // Notify all jury members
        $juryMembers = collect([
            $defense->juryPresident,
            $defense->juryExaminer,
            $defense->jurySupervisor
        ])->filter();

        foreach ($juryMembers as $juryMember) {
            if ($juryMember->id !== auth()->id()) {
                $this->notificationService->notify(
                    $juryMember,
                    'defense_completed',
                    'Defense finalized',
                    "Defense for {$defense->project->subject->title} has been finalized",
                    ['defense_id' => $defense->id]
                );
            }
        }
    }

    // Helper methods for data retrieval and exports
    private function getAverageGradeGiven($teacher): float
    {
        return Defense::join('defense_evaluations', 'defenses.id', '=', 'defense_evaluations.defense_id')
            ->where('defense_evaluations.evaluator_id', $teacher->id)
            ->avg('defense_evaluations.final_score') ?? 0;
    }

    private function getEvaluationSheets($defense): array
    {
        // Return paths to evaluation sheet templates
        return [];
    }

    private function getDefenseScheduleDocument($defense): ?string
    {
        // Return path to defense schedule document
        return null;
    }

    private function exportToIcal($defenses, $teacher)
    {
        // Implementation for iCal export
        // Return iCal file download
    }

    private function exportToPdf($defenses, $teacher)
    {
        // Implementation for PDF export
        // Return PDF file download
    }
}
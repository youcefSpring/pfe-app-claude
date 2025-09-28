<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateSubjectRequest;
use App\Http\Requests\PFE\UpdateSubjectRequest;
use App\Models\Subject;
use App\Models\TeamSubjectPreference;
use App\Services\SubjectService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class TeacherSubjectController extends Controller
{
    public function __construct(
        private SubjectService $subjectService,
        private NotificationService $notificationService
    ) {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    /**
     * Teacher subject management dashboard
     */
    public function index(Request $request): View
    {
        $teacher = auth()->user();

        $query = Subject::where('supervisor_id', $teacher->id)
            ->withCount(['teamPreferences', 'projects']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $subjects = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = $this->getSubjectStats($teacher);

        return view('pfe.teacher.subjects.index', [
            'subjects' => $subjects,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search'])
        ]);
    }

    /**
     * Enhanced subject creation form
     */
    public function create(): View
    {
        $templates = $this->getSubjectTemplates();
        $suggestedKeywords = $this->getSuggestedKeywords();
        $commonTools = $this->getCommonTools();

        return view('pfe.teacher.subjects.create', [
            'templates' => $templates,
            'suggested_keywords' => $suggestedKeywords,
            'common_tools' => $commonTools
        ]);
    }

    /**
     * Store new subject with enhanced features
     */
    public function store(CreateSubjectRequest $request): RedirectResponse
    {
        $subjectData = array_merge($request->validated(), [
            'supervisor_id' => auth()->id(),
            'status' => 'draft'
        ]);

        // Handle file attachments (specifications, resources)
        if ($request->hasFile('specification_file')) {
            $subjectData['specification_file_path'] = $request->file('specification_file')
                ->store("subjects/specifications", 'local');
        }

        if ($request->hasFile('resource_files')) {
            $resourcePaths = [];
            foreach ($request->file('resource_files') as $file) {
                $resourcePaths[] = $file->store("subjects/resources", 'local');
            }
            $subjectData['resource_file_paths'] = $resourcePaths;
        }

        $subject = $this->subjectService->createSubject($subjectData, auth()->user());

        return redirect()->route('pfe.teacher.subjects.show', $subject)
            ->with('success', 'Subject created successfully');
    }

    /**
     * Enhanced subject view for teachers
     */
    public function show(Subject $subject): View
    {
        $this->authorize('view', $subject);

        $subject->load([
            'teamPreferences.team.members.user',
            'projects.team',
            'validator'
        ]);

        $interestAnalytics = $this->getSubjectInterestAnalytics($subject);
        $competingTeams = $this->getCompetingTeams($subject);
        $feedback = $this->getValidationFeedback($subject);

        return view('pfe.teacher.subjects.show', [
            'subject' => $subject,
            'interest_analytics' => $interestAnalytics,
            'competing_teams' => $competingTeams,
            'validation_feedback' => $feedback
        ]);
    }

    /**
     * Subject interest tracking
     */
    public function trackInterest(Subject $subject): View
    {
        $this->authorize('view', $subject);

        $interestedTeams = TeamSubjectPreference::where('subject_id', $subject->id)
            ->with(['team.members.user', 'team.leader'])
            ->orderBy('preference_order')
            ->get();

        $interestTrends = $this->getInterestTrends($subject);
        $competitionAnalysis = $this->analyzeCompetition($subject);

        return view('pfe.teacher.subjects.interest', [
            'subject' => $subject,
            'interested_teams' => $interestedTeams,
            'interest_trends' => $interestTrends,
            'competition_analysis' => $competitionAnalysis
        ]);
    }

    /**
     * Submit subject for validation
     */
    public function submitForValidation(Subject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        if ($subject->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft subjects can be submitted for validation']);
        }

        // Validate subject completeness
        $validationErrors = $this->validateSubjectCompleteness($subject);
        if (!empty($validationErrors)) {
            return back()->withErrors($validationErrors);
        }

        $subject->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        // Notify department head/chef master
        $chefMaster = User::role('chef_master')
            ->where('department', auth()->user()->department)
            ->first();

        if ($chefMaster) {
            $this->notificationService->notify(
                $chefMaster,
                'subject_submitted',
                'New subject for validation',
                "Subject '{$subject->title}' submitted by {$subject->supervisor->first_name} {$subject->supervisor->last_name}",
                ['subject_id' => $subject->id]
            );
        }

        return back()->with('success', 'Subject submitted for validation successfully');
    }

    /**
     * Clone existing subject as template
     */
    public function clone(Subject $sourceSubject): RedirectResponse
    {
        $this->authorize('create', Subject::class);

        $newSubject = $sourceSubject->replicate([
            'id', 'status', 'validated_by', 'validated_at', 'validation_notes',
            'submitted_at', 'created_at', 'updated_at'
        ]);

        $newSubject->title = $sourceSubject->title . ' (Copy)';
        $newSubject->supervisor_id = auth()->id();
        $newSubject->status = 'draft';
        $newSubject->save();

        return redirect()->route('pfe.teacher.subjects.edit', $newSubject)
            ->with('success', 'Subject cloned successfully. Please review and modify as needed.');
    }

    /**
     * Archive/deactivate subject
     */
    public function archive(Subject $subject): RedirectResponse
    {
        $this->authorize('update', $subject);

        if ($subject->projects()->exists()) {
            return back()->withErrors(['error' => 'Cannot archive subject with active projects']);
        }

        $subject->update(['status' => 'archived']);

        return back()->with('success', 'Subject archived successfully');
    }

    /**
     * Get subject statistics for teacher
     */
    private function getSubjectStats($teacher): array
    {
        $totalSubjects = Subject::where('supervisor_id', $teacher->id)->count();
        $approvedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'approved')->count();
        $publishedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->where('status', 'published')->count();
        $assignedSubjects = Subject::where('supervisor_id', $teacher->id)
            ->whereHas('projects')->count();

        return [
            'total_subjects' => $totalSubjects,
            'approved_subjects' => $approvedSubjects,
            'published_subjects' => $publishedSubjects,
            'assigned_subjects' => $assignedSubjects,
            'approval_rate' => $totalSubjects > 0 ? ($approvedSubjects / $totalSubjects) * 100 : 0,
            'assignment_rate' => $publishedSubjects > 0 ? ($assignedSubjects / $publishedSubjects) * 100 : 0
        ];
    }

    /**
     * Get subject interest analytics
     */
    private function getSubjectInterestAnalytics(Subject $subject): array
    {
        $preferences = TeamSubjectPreference::where('subject_id', $subject->id)
            ->with('team')
            ->get();

        $interestLevel = $preferences->count();
        $averagePreferenceOrder = $preferences->avg('preference_order') ?? 0;

        $preferenceDistribution = $preferences->groupBy('preference_order')
            ->map(function($group) {
                return $group->count();
            })->toArray();

        return [
            'total_interested_teams' => $interestLevel,
            'average_preference_order' => round($averagePreferenceOrder, 1),
            'preference_distribution' => $preferenceDistribution,
            'interest_level' => $this->categorizeInterestLevel($interestLevel),
            'competition_intensity' => $this->calculateCompetitionIntensity($subject)
        ];
    }

    /**
     * Get competing teams for subject
     */
    private function getCompetingTeams(Subject $subject)
    {
        return TeamSubjectPreference::where('subject_id', $subject->id)
            ->with(['team.members.user', 'team.leader'])
            ->orderBy('preference_order')
            ->get()
            ->map(function($preference) {
                $team = $preference->team;
                $team->preference_order = $preference->preference_order;
                $team->team_score = $this->calculateTeamCompetitiveScore($team);
                return $team;
            });
    }

    /**
     * Get validation feedback
     */
    private function getValidationFeedback(Subject $subject): ?array
    {
        if (!$subject->validation_notes || !$subject->validator) {
            return null;
        }

        return [
            'validator' => $subject->validator,
            'notes' => $subject->validation_notes,
            'validated_at' => $subject->validated_at,
            'action_required' => $subject->status === 'needs_correction'
        ];
    }

    /**
     * Get subject templates
     */
    private function getSubjectTemplates(): array
    {
        return [
            'web_development' => [
                'title' => 'Web Application Development Template',
                'keywords' => ['Web Development', 'Frontend', 'Backend', 'Database'],
                'tools' => ['HTML/CSS', 'JavaScript', 'Framework (React/Vue/Angular)', 'Backend (Laravel/Node.js)', 'Database (MySQL/PostgreSQL)']
            ],
            'mobile_app' => [
                'title' => 'Mobile Application Development Template',
                'keywords' => ['Mobile Development', 'iOS', 'Android', 'Cross-platform'],
                'tools' => ['React Native/Flutter', 'Native iOS/Android', 'Backend API', 'Database']
            ],
            'ai_ml' => [
                'title' => 'AI/Machine Learning Project Template',
                'keywords' => ['Machine Learning', 'AI', 'Data Science', 'Deep Learning'],
                'tools' => ['Python', 'TensorFlow/PyTorch', 'Jupyter', 'Data visualization tools']
            ],
            'data_analysis' => [
                'title' => 'Data Analysis Project Template',
                'keywords' => ['Data Analysis', 'Statistics', 'Visualization', 'Business Intelligence'],
                'tools' => ['Python/R', 'SQL', 'Tableau/Power BI', 'Statistical software']
            ]
        ];
    }

    /**
     * Validate subject completeness before submission
     */
    private function validateSubjectCompleteness(Subject $subject): array
    {
        $errors = [];

        if (strlen($subject->description) < 100) {
            $errors['description'] = 'Description must be at least 100 characters';
        }

        if (empty($subject->keywords) || count($subject->keywords) < 3) {
            $errors['keywords'] = 'At least 3 keywords are required';
        }

        if (empty($subject->required_tools) || count($subject->required_tools) < 2) {
            $errors['tools'] = 'At least 2 required tools must be specified';
        }

        if (!$subject->max_teams || $subject->max_teams < 1) {
            $errors['max_teams'] = 'Maximum number of teams must be specified';
        }

        return $errors;
    }

    // Helper methods for analytics and calculations
    private function getSuggestedKeywords(): array
    {
        return [
            'Programming' => ['Java', 'Python', 'JavaScript', 'C++', 'C#', 'PHP'],
            'Technologies' => ['React', 'Vue.js', 'Angular', 'Laravel', 'Spring', 'Django'],
            'Domains' => ['Web Development', 'Mobile Development', 'AI/ML', 'Data Science', 'Cybersecurity'],
            'Tools' => ['Git', 'Docker', 'Kubernetes', 'MySQL', 'PostgreSQL', 'MongoDB']
        ];
    }

    private function getCommonTools(): array
    {
        return [
            'Development' => ['Visual Studio Code', 'IntelliJ IDEA', 'Git', 'Docker'],
            'Frontend' => ['HTML/CSS', 'JavaScript', 'React', 'Vue.js', 'Angular'],
            'Backend' => ['Laravel', 'Spring Boot', 'Django', 'Node.js', 'Express'],
            'Database' => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
            'Design' => ['Figma', 'Adobe XD', 'Sketch', 'Photoshop']
        ];
    }

    private function categorizeInterestLevel(int $count): string
    {
        if ($count >= 10) return 'Very High';
        if ($count >= 5) return 'High';
        if ($count >= 2) return 'Moderate';
        if ($count === 1) return 'Low';
        return 'No Interest';
    }

    private function calculateCompetitionIntensity(Subject $subject): string
    {
        $interestCount = $subject->teamPreferences()->count();
        $maxTeams = $subject->max_teams;

        if ($interestCount <= $maxTeams) return 'No Competition';
        if ($interestCount <= $maxTeams * 2) return 'Moderate Competition';
        if ($interestCount <= $maxTeams * 3) return 'High Competition';
        return 'Very High Competition';
    }

    private function calculateTeamCompetitiveScore($team): float
    {
        // Simplified scoring algorithm
        $score = 0;

        // Formation timing (earlier = higher score)
        if ($team->created_at) {
            $daysAgo = now()->diffInDays($team->created_at);
            $score += max(0, 30 - $daysAgo);
        }

        // Team size (optimal = 3)
        $score += ($team->members_count == 3) ? 20 : 10;

        // Leader experience (mock)
        $score += rand(10, 30);

        return $score;
    }

    private function getInterestTrends(Subject $subject): array
    {
        // Track interest over time
        return TeamSubjectPreference::where('subject_id', $subject->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function analyzeCompetition(Subject $subject): array
    {
        $preferences = TeamSubjectPreference::where('subject_id', $subject->id)
            ->with('team')
            ->get();

        return [
            'total_competing_teams' => $preferences->count(),
            'max_assignable_teams' => $subject->max_teams,
            'competition_ratio' => $subject->max_teams > 0 ? $preferences->count() / $subject->max_teams : 0,
            'preference_breakdown' => $preferences->groupBy('preference_order')
                ->map(function($group) {
                    return $group->count();
                })->toArray()
        ];
    }
}
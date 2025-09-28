<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Defense;
use App\Models\Room;
use App\Models\User;
use App\Models\PfeProject;
use App\Services\DefenseSchedulingService;
use App\Services\PvGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DefenseSchedulingController extends Controller
{
    public function __construct(
        private DefenseSchedulingService $schedulingService,
        private PvGenerationService $pvService
    ) {
        $this->middleware('auth');
        $this->middleware('role:admin_pfe|chef_master');
    }

    /**
     * Defense scheduling dashboard
     */
    public function index(): View
    {
        $stats = $this->getSchedulingStats();
        $upcomingDefenses = Defense::where('defense_date', '>=', now())
            ->with(['project.team', 'project.subject', 'room', 'juryPresident', 'juryExaminer', 'jurySupervisor'])
            ->orderBy('defense_date')
            ->take(10)
            ->get();

        return view('pfe.defenses.scheduling.index', [
            'stats' => $stats,
            'upcoming_defenses' => $upcomingDefenses
        ]);
    }

    /**
     * Auto-scheduling interface
     */
    public function showAutoScheduling(): View
    {
        $readyProjects = PfeProject::where('status', 'ready_for_defense')
            ->with(['team', 'subject', 'supervisor'])
            ->get();

        $availableRooms = Room::where('is_active', true)
            ->where('type', 'defense')
            ->get();

        $availableJury = User::role(['teacher', 'professor'])
            ->where('is_active', true)
            ->get();

        $constraints = $this->getSchedulingConstraints();

        return view('pfe.defenses.scheduling.auto', [
            'ready_projects' => $readyProjects,
            'available_rooms' => $availableRooms,
            'available_jury' => $availableJury,
            'constraints' => $constraints
        ]);
    }

    /**
     * Execute auto-scheduling
     */
    public function executeAutoScheduling(Request $request): RedirectResponse
    {
        $request->validate([
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'time_slots' => 'required|array',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'exclude_weekends' => 'boolean',
            'min_break_minutes' => 'integer|min:0|max:120'
        ]);

        try {
            $results = $this->schedulingService->autoScheduleDefenses([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'time_slots' => $request->time_slots,
                'room_ids' => $request->room_ids,
                'exclude_weekends' => $request->exclude_weekends ?? true,
                'min_break_minutes' => $request->min_break_minutes ?? 30
            ]);

            session()->flash('scheduling_results', $results);

            return redirect()->route('pfe.defenses.scheduling.results')
                ->with('success', "Scheduled {$results['scheduled_count']} defenses successfully");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show scheduling results
     */
    public function showResults(): View
    {
        $results = session('scheduling_results', [
            'scheduled_defenses' => [],
            'conflicts' => [],
            'scheduled_count' => 0
        ]);

        return view('pfe.defenses.scheduling.results', [
            'results' => $results
        ]);
    }

    /**
     * Manual scheduling interface
     */
    public function showManualScheduling(): View
    {
        $unscheduledProjects = PfeProject::where('status', 'ready_for_defense')
            ->whereDoesntHave('defense')
            ->with(['team', 'subject', 'supervisor'])
            ->get();

        $rooms = Room::where('is_active', true)
            ->where('type', 'defense')
            ->get();

        $jury = User::role(['teacher', 'professor'])
            ->where('is_active', true)
            ->get();

        return view('pfe.defenses.scheduling.manual', [
            'unscheduled_projects' => $unscheduledProjects,
            'rooms' => $rooms,
            'jury' => $jury
        ]);
    }

    /**
     * Calendar view for defense scheduling
     */
    public function showCalendar(Request $request): View
    {
        $startDate = Carbon::parse($request->get('start', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end', now()->endOfMonth()));

        $defenses = Defense::whereBetween('defense_date', [$startDate, $endDate])
            ->with(['project.team', 'project.subject', 'room', 'juryPresident'])
            ->get();

        $rooms = Room::where('is_active', true)
            ->where('type', 'defense')
            ->get();

        return view('pfe.defenses.scheduling.calendar', [
            'defenses' => $defenses,
            'rooms' => $rooms,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Check availability for specific date/time
     */
    public function checkAvailability(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_id' => 'required|exists:rooms,id',
            'jury_ids' => 'required|array',
            'jury_ids.*' => 'exists:users,id'
        ]);

        $conflicts = $this->schedulingService->checkAvailability(
            Carbon::parse($request->date . ' ' . $request->start_time),
            Carbon::parse($request->date . ' ' . $request->end_time),
            $request->room_id,
            $request->jury_ids
        );

        return response()->json([
            'available' => empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Bulk operations on defenses
     */
    public function bulkOperations(Request $request): RedirectResponse
    {
        $request->validate([
            'operation' => 'required|in:reschedule,cancel,generate_pv,notify',
            'defense_ids' => 'required|array',
            'defense_ids.*' => 'exists:defenses,id'
        ]);

        $defenses = Defense::whereIn('id', $request->defense_ids)->get();
        $results = [];

        switch ($request->operation) {
            case 'reschedule':
                $results = $this->bulkReschedule($defenses, $request->all());
                break;

            case 'cancel':
                $results = $this->bulkCancel($defenses, $request->reason);
                break;

            case 'generate_pv':
                $results = $this->bulkGeneratePv($defenses);
                break;

            case 'notify':
                $results = $this->bulkNotify($defenses, $request->message);
                break;
        }

        return back()->with('success', "Bulk operation completed. Processed {$results['success_count']} items.");
    }

    /**
     * Export defense schedule
     */
    public function exportSchedule(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $format = $request->get('format', 'pdf');
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        $defenses = Defense::whereBetween('defense_date', [$startDate, $endDate])
            ->with(['project.team', 'project.subject', 'room', 'juryPresident', 'juryExaminer', 'jurySupervisor'])
            ->orderBy('defense_date')
            ->get();

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($defenses, $startDate, $endDate);

            case 'ical':
                return $this->exportToIcal($defenses);

            default:
                return $this->exportToPdf($defenses, $startDate, $endDate);
        }
    }

    /**
     * Get scheduling statistics
     */
    private function getSchedulingStats(): array
    {
        $totalProjects = PfeProject::count();
        $readyForDefense = PfeProject::where('status', 'ready_for_defense')->count();
        $scheduledDefenses = Defense::whereNotNull('defense_date')->count();
        $completedDefenses = Defense::where('status', 'completed')->count();

        return [
            'total_projects' => $totalProjects,
            'ready_for_defense' => $readyForDefense,
            'scheduled_defenses' => $scheduledDefenses,
            'completed_defenses' => $completedDefenses,
            'scheduling_rate' => $readyForDefense > 0 ? ($scheduledDefenses / $readyForDefense) * 100 : 0,
            'completion_rate' => $scheduledDefenses > 0 ? ($completedDefenses / $scheduledDefenses) * 100 : 0,
            'avg_defense_duration' => $this->getAverageDefenseDuration(),
            'upcoming_week' => Defense::whereBetween('defense_date', [now(), now()->addWeek()])->count()
        ];
    }

    /**
     * Get scheduling constraints
     */
    private function getSchedulingConstraints(): array
    {
        return [
            'defense_duration' => config('defense.default_duration', 60), // minutes
            'break_between_defenses' => config('defense.break_duration', 30), // minutes
            'daily_start_time' => config('defense.daily_start', '08:00'),
            'daily_end_time' => config('defense.daily_end', '18:00'),
            'excluded_dates' => $this->getExcludedDates(),
            'jury_availability' => $this->getJuryAvailability()
        ];
    }

    /**
     * Get excluded dates (holidays, weekends if configured)
     */
    private function getExcludedDates(): array
    {
        // This would typically come from a holidays table or configuration
        return [
            // Add holidays and excluded dates
        ];
    }

    /**
     * Get jury availability constraints
     */
    private function getJuryAvailability(): array
    {
        // This would check jury member availability from their calendars
        return [];
    }

    /**
     * Get average defense duration
     */
    private function getAverageDefenseDuration(): int
    {
        $avgMinutes = Defense::where('status', 'completed')
            ->whereNotNull('actual_duration')
            ->avg('actual_duration');

        return (int) ($avgMinutes ?? 60);
    }

    /**
     * Bulk reschedule defenses
     */
    private function bulkReschedule($defenses, $data): array
    {
        $successCount = 0;
        // Implementation for bulk rescheduling
        return ['success_count' => $successCount];
    }

    /**
     * Bulk cancel defenses
     */
    private function bulkCancel($defenses, $reason): array
    {
        $successCount = 0;
        foreach ($defenses as $defense) {
            $defense->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id()
            ]);
            $successCount++;
        }

        return ['success_count' => $successCount];
    }

    /**
     * Bulk generate PV documents
     */
    private function bulkGeneratePv($defenses): array
    {
        $successCount = 0;
        foreach ($defenses as $defense) {
            if ($defense->status === 'completed') {
                try {
                    $this->pvService->generateDefensePv($defense);
                    $successCount++;
                } catch (\Exception $e) {
                    // Log error
                }
            }
        }

        return ['success_count' => $successCount];
    }

    /**
     * Bulk notify participants
     */
    private function bulkNotify($defenses, $message): array
    {
        $successCount = 0;
        // Implementation for bulk notifications
        return ['success_count' => $successCount];
    }

    /**
     * Export schedule to PDF
     */
    private function exportToPdf($defenses, $startDate, $endDate)
    {
        $pdf = \PDF::loadView('pdf.defense-schedule', [
            'defenses' => $defenses,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $pdf->download("defense_schedule_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.pdf");
    }

    /**
     * Export schedule to Excel
     */
    private function exportToExcel($defenses, $startDate, $endDate)
    {
        // Implementation using Laravel Excel or similar
        // Return Excel file download
    }

    /**
     * Export schedule to iCal format
     */
    private function exportToIcal($defenses)
    {
        // Implementation for iCal export
        // Return .ics file download
    }
}
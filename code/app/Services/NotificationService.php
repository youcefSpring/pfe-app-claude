<?php

namespace App\Services;

use App\Models\PfeNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class NotificationService
{
    public function createNotification(int $userId, string $type, string $title, string $message, array $data = []): PfeNotification
    {
        return PfeNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function createBulkNotifications(array $userIds, string $type, string $title, string $message, array $data = []): int
    {
        $notifications = [];
        $timestamp = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'created_at' => $timestamp
            ];
        }

        PfeNotification::insert($notifications);

        return count($notifications);
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        return PfeNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]) > 0;
    }

    public function markAllAsRead(int $userId): int
    {
        return PfeNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getUnreadNotifications(int $userId, int $limit = 10): Collection
    {
        return PfeNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getNotificationHistory(int $userId, int $page = 1, int $perPage = 20): Collection
    {
        return PfeNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
    }

    public function sendEmailNotification(int $userId, string $subject, string $template, array $data = []): bool
    {
        $user = User::find($userId);
        if (!$user || !$user->email) {
            return false;
        }

        try {
            Mail::send($template, $data, function ($message) use ($user, $subject) {
                $message->to($user->email)
                    ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Email notification failed', [
                'user_id' => $userId,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function processAutomaticNotifications(): array
    {
        $processed = [];

        // Check for upcoming defenses (24h reminder)
        $processed['defense_reminders'] = $this->sendDefenseReminders();

        // Check for overdue deliverables
        $processed['overdue_deliverables'] = $this->sendOverdueDeliverableNotifications();

        // Check for pending subject validations
        $processed['pending_validations'] = $this->sendPendingValidationReminders();

        // Check for team formation deadlines
        $processed['team_formation'] = $this->sendTeamFormationReminders();

        return $processed;
    }

    public function getNotificationStats(int $userId): array
    {
        $stats = PfeNotification::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN read_at IS NULL THEN 1 END) as unread,
                COUNT(CASE WHEN read_at IS NOT NULL THEN 1 END) as read
            ')
            ->first();

        return [
            'total' => $stats->total,
            'unread' => $stats->unread,
            'read' => $stats->read
        ];
    }

    public function deleteOldNotifications(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);

        return PfeNotification::where('created_at', '<', $cutoffDate)
            ->where('read_at', '!=', null) // Only delete read notifications
            ->delete();
    }

    private function sendDefenseReminders(): int
    {
        $tomorrowDefenses = DB::table('defenses')
            ->join('projects', 'defenses.project_id', '=', 'projects.id')
            ->join('teams', 'projects.team_id', '=', 'teams.id')
            ->join('team_members', 'teams.id', '=', 'team_members.team_id')
            ->join('subjects', 'projects.subject_id', '=', 'subjects.id')
            ->where('defenses.defense_date', now()->addDay()->format('Y-m-d'))
            ->where('defenses.status', 'confirmed')
            ->select([
                'team_members.user_id',
                'defenses.defense_date',
                'defenses.start_time',
                'subjects.title as subject_title'
            ])
            ->get();

        $count = 0;
        foreach ($tomorrowDefenses as $defense) {
            $this->createNotification(
                $defense->user_id,
                'defense_reminder',
                'Defense Tomorrow',
                "Your defense for '{$defense->subject_title}' is scheduled tomorrow at {$defense->start_time}",
                ['defense_date' => $defense->defense_date, 'start_time' => $defense->start_time]
            );
            $count++;
        }

        return $count;
    }

    private function sendOverdueDeliverableNotifications(): int
    {
        // This would need actual deliverable deadlines in the system
        // For now, return 0 as placeholder
        return 0;
    }

    private function sendPendingValidationReminders(): int
    {
        $pendingSubjects = DB::table('subjects')
            ->join('users as supervisors', 'subjects.supervisor_id', '=', 'supervisors.id')
            ->where('subjects.status', 'submitted')
            ->where('subjects.created_at', '<', now()->subDays(3)) // Pending for 3+ days
            ->select(['subjects.id', 'subjects.title', 'supervisors.department'])
            ->get();

        $chefMasters = User::role('chef_master')->get()->groupBy('department');
        $count = 0;

        foreach ($pendingSubjects as $subject) {
            $departmentHeads = $chefMasters->get($subject->department, collect());

            foreach ($departmentHeads as $head) {
                $this->createNotification(
                    $head->id,
                    'pending_validation_reminder',
                    'Pending Subject Validation',
                    "Subject '{$subject->title}' has been pending validation for over 3 days",
                    ['subject_id' => $subject->id]
                );
                $count++;
            }
        }

        return $count;
    }

    private function sendTeamFormationReminders(): int
    {
        // Students without teams approaching deadline
        $studentsWithoutTeams = User::role('student')
            ->whereDoesntHave('teamMemberships')
            ->get();

        $count = 0;
        foreach ($studentsWithoutTeams as $student) {
            $this->createNotification(
                $student->id,
                'team_formation_reminder',
                'Team Formation Reminder',
                'You need to join or create a team for your PFE project',
                []
            );
            $count++;
        }

        return $count;
    }
}
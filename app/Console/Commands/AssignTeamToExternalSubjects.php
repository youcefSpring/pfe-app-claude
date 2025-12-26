<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subject;
use App\Models\TeamMember;

class AssignTeamToExternalSubjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subjects:assign-teams
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign team_id to existing external subjects based on student creator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting assignment of teams to external subjects...');

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be saved');
        }

        // Find external subjects without team_id
        $subjectsWithoutTeam = Subject::where('is_external', true)
            ->whereNull('team_id')
            ->whereNotNull('student_id')
            ->with('student')
            ->get();

        if ($subjectsWithoutTeam->isEmpty()) {
            $this->info('No external subjects found without team_id');
            return 0;
        }

        $this->info("Found {$subjectsWithoutTeam->count()} external subjects without team_id");

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($subjectsWithoutTeam as $subject) {
            $studentName = $subject->student->name ?? 'Unknown';

            // Find the team that the student belongs to
            $teamMember = TeamMember::where('student_id', $subject->student_id)->first();

            if (!$teamMember) {
                $this->warn("  ✗ Subject '{$subject->title}' - Student '{$studentName}' is not in any team");
                $skipped++;
                continue;
            }

            $teamName = $teamMember->team->name ?? 'Unknown Team';

            if (!$dryRun) {
                try {
                    $subject->team_id = $teamMember->team_id;
                    $subject->save();
                    $this->info("  ✓ Subject '{$subject->title}' assigned to team '{$teamName}'");
                    $updated++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Error updating subject '{$subject->title}': {$e->getMessage()}");
                    $errors++;
                }
            } else {
                $this->info("  [DRY RUN] Would assign '{$subject->title}' to team '{$teamName}'");
                $updated++;
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated' . ($dryRun ? ' (would be)' : ''), $updated],
                ['Skipped (no team)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->comment('Run without --dry-run to actually update the database:');
            $this->comment('  php artisan subjects:assign-teams');
        } else {
            $this->newLine();
            $this->info('✓ Team assignment completed!');
        }

        return 0;
    }
}

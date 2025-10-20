<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SubjectAllocationService;

class AllocateSubjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subjects:allocate {--force : Force allocation even if deadline hasn\'t passed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically allocate subjects to teams based on their preferences and grades after deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $allocationService = new SubjectAllocationService();

        // Check if allocation can be performed
        if (!$this->option('force') && !$allocationService->canPerformAllocation()) {
            $this->error('Automatic allocation cannot be performed yet. Deadline may not have passed or allocation already completed.');
            return 1;
        }

        $this->info('Starting automatic subject allocation...');

        // Run the allocation
        $results = $allocationService->runPreferenceBasedAllocation();

        // Display results
        $this->displayResults($results);

        return 0;
    }

    /**
     * Display allocation results.
     */
    private function displayResults(array $results): void
    {
        $this->line('');
        $this->info('=== ALLOCATION RESULTS ===');
        $this->line("Total teams: {$results['total_teams']}");
        $this->line("Allocated teams: {$results['allocated_teams']}");
        $this->line("Unallocated teams: " . count($results['unallocated_teams']));

        if (!empty($results['allocations'])) {
            $this->line('');
            $this->info('✅ SUCCESSFUL ALLOCATIONS:');
            foreach ($results['allocations'] as $allocation) {
                $this->line(sprintf(
                    "  • %s → %s (Choice #%d, Average: %.2f)",
                    $allocation['team_name'],
                    $allocation['subject_title'],
                    $allocation['preference_order'],
                    $allocation['team_average']
                ));
            }
        }

        if (!empty($results['unallocated_teams'])) {
            $this->line('');
            $this->warn('❌ UNALLOCATED TEAMS:');
            foreach ($results['unallocated_teams'] as $unallocated) {
                $this->line(sprintf(
                    "  • %s - %s",
                    $unallocated['team']->name,
                    $unallocated['reason']
                ));
            }
        }

        if (!empty($results['errors'])) {
            $this->line('');
            $this->error('⚠️  ERRORS:');
            foreach ($results['errors'] as $error) {
                $this->line("  • $error");
            }
        }

        $this->line('');
        $this->info('Allocation process completed!');
    }
}
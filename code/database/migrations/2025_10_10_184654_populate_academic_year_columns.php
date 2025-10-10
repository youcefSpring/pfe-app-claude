<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get current academic year or create a default one
        $currentYear = AcademicYear::getCurrentYear();
        if (!$currentYear) {
            $currentYear = AcademicYear::create([
                'year' => '2024-2025',
                'title' => 'Academic Year 2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'status' => 'active',
                'is_current' => true,
                'description' => 'Default academic year'
            ]);
        }

        $defaultYear = $currentYear->year;

        // Update existing records with current academic year
        DB::table('subjects')->whereNull('academic_year')->update(['academic_year' => $defaultYear]);
        DB::table('teams')->whereNull('academic_year')->update(['academic_year' => $defaultYear]);
        DB::table('projects')->whereNull('academic_year')->update(['academic_year' => $defaultYear]);

        // Update defenses with academic year and determine session based on defense date
        $defenses = DB::table('defenses')->whereNull('academic_year')->get();

        foreach ($defenses as $defense) {
            $defenseDate = \Carbon\Carbon::parse($defense->defense_date);
            $currentAcademicYear = AcademicYear::where('year', $defaultYear)->first();

            // Determine session based on whether defense is after academic year end date
            $session = 'session_1';
            if ($currentAcademicYear && $defenseDate->gt($currentAcademicYear->end_date)) {
                $session = 'session_2';
            }

            DB::table('defenses')
                ->where('id', $defense->id)
                ->update([
                    'academic_year' => $defaultYear,
                    'session' => $session
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it populates data
    }
};
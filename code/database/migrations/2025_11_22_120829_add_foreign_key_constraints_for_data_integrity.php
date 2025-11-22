<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('defenses', function (Blueprint $table) {
            // Ensure defense has valid project and subject
            if (!Schema::hasColumn('defenses', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Add check constraint for valid status values
            DB::statement("ALTER TABLE defenses ADD CONSTRAINT check_defense_status CHECK (status IN ('scheduled', 'in_progress', 'completed', 'cancelled'))");
            
            // Ensure defense date is in the future when created (handled by application logic)
            // Ensure all required fields are present for scheduled defenses
        });

        Schema::table('projects', function (Blueprint $table) {
            // Ensure project has valid team and subject
            // Add check constraint for valid status
            DB::statement("ALTER TABLE projects ADD CONSTRAINT check_project_status CHECK (status IN ('assigned', 'in_progress', 'submitted', 'defended', 'cancelled'))");
            
            // Ensure project has either subject_id or external_project_id
            DB::statement("ALTER TABLE projects ADD CONSTRAINT check_project_has_subject_or_external CHECK (subject_id IS NOT NULL OR external_project_id IS NOT NULL)");
        });

        Schema::table('teams', function (Blueprint $table) {
            // Add check constraint for valid status
            DB::statement("ALTER TABLE teams ADD CONSTRAINT check_team_status CHECK (status IN ('forming', 'complete', 'subject_selected', 'assigned', 'active', 'disbanded'))");
        });

        Schema::table('team_members', function (Blueprint $table) {
            // Ensure team member has valid role
            DB::statement("ALTER TABLE team_members ADD CONSTRAINT check_team_member_role CHECK (role IN ('leader', 'member'))");
            
            // Ensure unique team membership per student (one student can only be in one team)
            if (!Schema::hasIndex('team_members', 'team_members_student_id_unique')) {
                // Note: This is handled by application logic to allow historical data
                // $table->unique('student_id');
            }
        });

        Schema::table('defense_juries', function (Blueprint $table) {
            // Ensure jury member has valid role
            DB::statement("ALTER TABLE defense_juries ADD CONSTRAINT check_jury_role CHECK (role IN ('supervisor', 'president', 'examiner'))");
        });

        Schema::table('team_subject_preferences', function (Blueprint $table) {
            // Ensure preference order is between 1 and 10
            DB::statement("ALTER TABLE team_subject_preferences ADD CONSTRAINT check_preference_order CHECK (preference_order BETWEEN 1 AND 10)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop check constraints
        DB::statement("ALTER TABLE defenses DROP CONSTRAINT IF EXISTS check_defense_status");
        DB::statement("ALTER TABLE projects DROP CONSTRAINT IF EXISTS check_project_status");
        DB::statement("ALTER TABLE projects DROP CONSTRAINT IF EXISTS check_project_has_subject_or_external");
        DB::statement("ALTER TABLE teams DROP CONSTRAINT IF EXISTS check_team_status");
        DB::statement("ALTER TABLE team_members DROP CONSTRAINT IF EXISTS check_team_member_role");
        DB::statement("ALTER TABLE defense_juries DROP CONSTRAINT IF EXISTS check_jury_role");
        DB::statement("ALTER TABLE team_subject_preferences DROP CONSTRAINT IF EXISTS check_preference_order");
    }
};

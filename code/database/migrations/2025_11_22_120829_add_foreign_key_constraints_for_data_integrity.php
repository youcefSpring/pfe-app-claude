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
        // Clean up existing data before applying constraints
        
        // Fix invalid defense statuses
        DB::table('defenses')
            ->whereNotIn('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])
            ->update(['status' => 'scheduled']);
        
        // Fix invalid project statuses
        DB::table('projects')
            ->whereNotIn('status', ['assigned', 'in_progress', 'submitted', 'defended', 'cancelled'])
            ->update(['status' => 'assigned']);
        
        // Fix invalid team statuses
        DB::table('teams')
            ->whereNotIn('status', ['forming', 'complete', 'subject_selected', 'assigned', 'active', 'disbanded'])
            ->update(['status' => 'forming']);
        
        // Fix invalid team member roles
        DB::table('team_members')
            ->whereNotIn('role', ['leader', 'member'])
            ->update(['role' => 'member']);
        
        // Fix invalid jury roles
        DB::table('defense_juries')
            ->whereNotIn('role', ['supervisor', 'president', 'examiner'])
            ->update(['role' => 'examiner']);
        
        // Fix invalid preference orders
        DB::table('team_subject_preferences')
            ->where(function($q) {
                $q->where('preference_order', '<', 1)
                  ->orWhere('preference_order', '>', 10);
            })
            ->update(['preference_order' => 1]);
        
        // Drop existing constraints if they exist (for re-running migration)
        try { DB::statement("ALTER TABLE defenses DROP CHECK check_defense_status"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE projects DROP CHECK check_project_status"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE teams DROP CHECK check_team_status"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE team_members DROP CHECK check_team_member_role"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE defense_juries DROP CHECK check_jury_role"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE team_subject_preferences DROP CHECK check_preference_order"); } catch (\Exception $e) {}
        
        // Now apply constraints
        DB::statement("ALTER TABLE defenses ADD CONSTRAINT check_defense_status CHECK (status IN ('scheduled', 'in_progress', 'completed', 'cancelled'))");
        DB::statement("ALTER TABLE projects ADD CONSTRAINT check_project_status CHECK (status IN ('assigned', 'in_progress', 'submitted', 'defended', 'cancelled'))");
        DB::statement("ALTER TABLE teams ADD CONSTRAINT check_team_status CHECK (status IN ('forming', 'complete', 'subject_selected', 'assigned', 'active', 'disbanded'))");
        DB::statement("ALTER TABLE team_members ADD CONSTRAINT check_team_member_role CHECK (role IN ('leader', 'member'))");
        DB::statement("ALTER TABLE defense_juries ADD CONSTRAINT check_jury_role CHECK (role IN ('supervisor', 'president', 'examiner'))");
        DB::statement("ALTER TABLE team_subject_preferences ADD CONSTRAINT check_preference_order CHECK (preference_order BETWEEN 1 AND 10)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop check constraints using correct MySQL syntax
        try { DB::statement("ALTER TABLE defenses DROP CHECK check_defense_status"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE projects DROP CHECK check_project_status"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE teams DROP CHECK check_team_status"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE team_members DROP CHECK check_team_member_role"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE defense_juries DROP CHECK check_jury_role"); } catch (\Exception $e) {}
        try { DB::statement("ALTER TABLE team_subject_preferences DROP CHECK check_preference_order"); } catch (\Exception $e) {}
    }
};

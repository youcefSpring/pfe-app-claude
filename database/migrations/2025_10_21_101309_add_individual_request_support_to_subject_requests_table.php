<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subject_requests', function (Blueprint $table) {
            // Add work preference column for individual requests
            $table->enum('work_preference', ['individual', 'open_to_team'])->nullable()->after('request_message');

            // Make team_id nullable to support individual requests
            $table->foreignId('team_id')->nullable()->change();

            // Drop the unique constraint that requires team_id and recreate it with different conditions
            $table->dropUnique(['team_id', 'subject_id', 'status']);
        });

        // Add a new unique constraint that handles both team and individual requests
        Schema::table('subject_requests', function (Blueprint $table) {
            // For team requests: team_id + subject_id + status must be unique
            // For individual requests: requested_by + subject_id + status must be unique
            $table->unique(['requested_by', 'subject_id', 'status'], 'individual_subject_request_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_requests', function (Blueprint $table) {
            // Remove the new constraint
            $table->dropUnique('individual_subject_request_unique');

            // Remove work preference column
            $table->dropColumn('work_preference');

            // Make team_id required again
            $table->foreignId('team_id')->nullable(false)->change();

            // Restore original unique constraint
            $table->unique(['team_id', 'subject_id', 'status']);
        });
    }
};

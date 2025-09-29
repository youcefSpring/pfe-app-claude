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
        Schema::table('deliverables', function (Blueprint $table) {
            $table->foreignId('milestone_id')->nullable()->after('project_id')->constrained('project_milestones')->onDelete('set null');
            $table->integer('sprint_number')->nullable()->after('milestone_id');
            $table->decimal('version_number', 3, 1)->default(1.0)->after('sprint_number');
            $table->datetime('deadline')->nullable()->after('version_number');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('deadline');
            $table->json('acceptance_criteria')->nullable()->after('priority');
            $table->json('feedback_summary')->nullable()->after('acceptance_criteria');
            $table->boolean('revision_requested')->default(false)->after('feedback_summary');
            $table->datetime('approved_at')->nullable()->after('revision_requested');

            // Add indexes for better query performance
            $table->index(['project_id', 'status']);
            $table->index(['milestone_id', 'status']);
            $table->index(['sprint_number', 'project_id']);
            $table->index(['deadline', 'status']);
            $table->index(['priority', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['milestone_id', 'status']);
            $table->dropIndex(['sprint_number', 'project_id']);
            $table->dropIndex(['deadline', 'status']);
            $table->dropIndex(['priority', 'status']);

            $table->dropColumn([
                'milestone_id',
                'sprint_number',
                'version_number',
                'deadline',
                'priority',
                'acceptance_criteria',
                'feedback_summary',
                'revision_requested',
                'approved_at'
            ]);
        });
    }
};
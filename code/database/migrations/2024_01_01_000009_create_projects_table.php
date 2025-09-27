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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('team_id')->constrained('teams');
            $table->foreignId('supervisor_id')->constrained('users');
            $table->string('external_supervisor', 100)->nullable();
            $table->string('external_company', 100)->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'under_review', 'needs_revision', 'ready_for_defense', 'defended', 'completed'])->default('assigned');
            $table->date('start_date');
            $table->date('expected_end_date');
            $table->date('actual_end_date')->nullable();
            $table->decimal('final_grade', 4, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique('subject_id');
            $table->unique('team_id');
            $table->index('status');
            $table->index('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
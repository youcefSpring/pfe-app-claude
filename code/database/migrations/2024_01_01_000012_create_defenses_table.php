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
        Schema::create('defenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('room_id')->constrained('rooms');
            $table->date('defense_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedTinyInteger('duration');
            $table->foreignId('jury_president_id')->constrained('users');
            $table->foreignId('jury_examiner_id')->constrained('users');
            $table->foreignId('jury_supervisor_id')->constrained('users');
            $table->enum('status', ['scheduled', 'confirmed', 'rescheduled', 'in_progress', 'completed', 'archived'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->decimal('final_grade', 4, 2)->nullable();
            $table->decimal('grade_president', 4, 2)->nullable();
            $table->decimal('grade_examiner', 4, 2)->nullable();
            $table->decimal('grade_supervisor', 4, 2)->nullable();
            $table->text('observations')->nullable();
            $table->boolean('pv_generated')->default(false);
            $table->string('pv_file_path', 500)->nullable();
            $table->timestamp('scheduled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique('project_id');
            $table->index('defense_date');
            $table->index('status');
            $table->index(['room_id', 'defense_date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defenses');
    }
};
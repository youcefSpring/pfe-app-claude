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
        Schema::create('allocation_deadlines', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "PFE Subject Selection 2024-2025"
            $table->string('academic_year'); // 2024-2025
            $table->string('level'); // L3, M1, M2, etc.
            $table->datetime('preferences_start'); // When students can start choosing
            $table->datetime('preferences_deadline'); // Deadline for student choices
            $table->datetime('grades_verification_deadline'); // Deadline for grade verification
            $table->datetime('allocation_date'); // When automatic allocation happens
            $table->enum('status', ['draft', 'active', 'preferences_closed', 'grades_pending', 'completed'])->default('draft');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['academic_year', 'level']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_deadlines');
    }
};

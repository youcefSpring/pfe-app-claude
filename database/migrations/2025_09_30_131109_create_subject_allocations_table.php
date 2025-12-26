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
        Schema::create('subject_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_deadline_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('student_preference_order'); // Which preference was this (1st, 2nd, etc.)
            $table->decimal('student_average', 5, 2); // Student's calculated average
            $table->integer('allocation_rank'); // Rank among students who got this subject
            $table->enum('allocation_method', ['preference', 'automatic', 'manual'])->default('automatic');
            $table->text('allocation_notes')->nullable();
            $table->enum('status', ['tentative', 'confirmed', 'rejected'])->default('tentative');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['allocation_deadline_id', 'student_id']);
            $table->index(['allocation_deadline_id', 'subject_id']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_allocations');
    }
};

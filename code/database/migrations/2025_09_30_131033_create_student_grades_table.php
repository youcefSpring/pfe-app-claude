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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('subject_name'); // Subject/course name
            $table->string('semester'); // S1, S2, S3, etc.
            $table->string('academic_year'); // 2023-2024
            $table->decimal('grade', 5, 2); // Grade out of 20 (or other scale)
            $table->decimal('coefficient', 3, 1)->default(1.0); // Subject coefficient/weight
            $table->enum('status', ['draft', 'submitted', 'verified', 'rejected'])->default('draft');
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};

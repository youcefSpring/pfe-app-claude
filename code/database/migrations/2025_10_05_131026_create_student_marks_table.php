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
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Student ID
            // $table->string('subject_name'); // Subject/Module name
            $table->decimal('mark', 5, 2); // Mark/Grade (e.g., 15.75 out of 20)
            $table->decimal('max_mark', 5, 2)->default(20.00); // Maximum possible mark
            $table->string('semester')->nullable(); // e.g., S1, S2, S3, etc.
            $table->string('academic_year')->nullable(); // e.g., 2023-2024
            $table->text('notes')->nullable(); // Additional notes
            $table->unsignedBigInteger('created_by'); // Admin who added the mark
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Ensure unique marks per student per subject per semester
            $table->unique(['user_id', 'subject_name', 'semester', 'academic_year'], 'unique_student_subject_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};

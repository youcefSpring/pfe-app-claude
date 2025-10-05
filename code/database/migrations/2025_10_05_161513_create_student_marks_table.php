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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subject_name')->nullable();
            $table->decimal('mark', 5, 2)->nullable();
            $table->decimal('max_mark', 5, 2)->default(20);
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Multiple marks support
            $table->decimal('mark_1', 5, 2)->nullable();
            $table->decimal('mark_2', 5, 2)->nullable();
            $table->decimal('mark_3', 5, 2)->nullable();
            $table->decimal('mark_4', 5, 2)->nullable();
            $table->decimal('mark_5', 5, 2)->nullable();

            $table->decimal('max_mark_1', 5, 2)->default(20);
            $table->decimal('max_mark_2', 5, 2)->default(20);
            $table->decimal('max_mark_3', 5, 2)->default(20);
            $table->decimal('max_mark_4', 5, 2)->default(20);
            $table->decimal('max_mark_5', 5, 2)->default(20);

            $table->decimal('weight_1', 5, 2)->default(0);
            $table->decimal('weight_2', 5, 2)->default(0);
            $table->decimal('weight_3', 5, 2)->default(0);
            $table->decimal('weight_4', 5, 2)->default(0);
            $table->decimal('weight_5', 5, 2)->default(0);

            $table->timestamps();

            $table->index(['user_id', 'academic_year', 'semester']);
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

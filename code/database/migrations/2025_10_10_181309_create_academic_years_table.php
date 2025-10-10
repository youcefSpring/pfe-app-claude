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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('year', 10)->unique(); // e.g., "2024-2025"
            $table->string('title')->nullable(); // e.g., "Academic Year 2024-2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->json('statistics')->nullable(); // Store year-end statistics
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('ended_by')->nullable();
            $table->timestamps();

            $table->foreign('ended_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'is_current']);
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};

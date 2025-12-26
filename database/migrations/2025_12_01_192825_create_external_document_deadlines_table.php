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
        Schema::create('external_document_deadlines', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "External Documents 2024-2025"
            $table->string('academic_year'); // 2024-2025
            $table->datetime('upload_start')->nullable(); // When admin can start uploading documents
            $table->datetime('upload_deadline')->nullable(); // Deadline for admin uploads
            $table->datetime('response_start')->nullable(); // When teams can start responding
            $table->datetime('response_deadline')->nullable(); // Deadline for team responses
            $table->enum('status', ['draft', 'active', 'upload_closed', 'response_closed', 'completed'])->default('draft');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['academic_year']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_document_deadlines');
    }
};

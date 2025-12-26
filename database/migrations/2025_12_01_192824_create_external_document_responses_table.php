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
        Schema::create('external_document_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_document_id')->constrained('external_documents')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_original_name');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('file_type', 10); // pdf, doc, docx
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->text('admin_feedback')->nullable();
            $table->foreignId('feedback_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('feedback_at')->nullable();
            $table->timestamps();

            // One response per team per document
            $table->unique(['external_document_id', 'team_id']);
            $table->index('external_document_id');
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_document_responses');
    }
};

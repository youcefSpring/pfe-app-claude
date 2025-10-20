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
        Schema::create('subject_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade'); // Team leader
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('request_message')->nullable(); // Why they want this subject
            $table->text('admin_response')->nullable(); // Admin's response if rejected
            $table->timestamp('requested_at')->default(now());
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who responded
            $table->timestamps();

            // Ensure a team can only have one pending request per subject
            $table->unique(['team_id', 'subject_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_requests');
    }
};

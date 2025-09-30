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
        Schema::create('external_projects', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('contact_person');
            $table->string('contact_email');
            $table->string('contact_phone', 20)->nullable();
            $table->text('project_description');
            $table->text('technologies')->nullable();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('assigned_supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['submitted', 'under_review', 'assigned', 'approved', 'rejected'])->default('submitted');
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_projects');
    }
};

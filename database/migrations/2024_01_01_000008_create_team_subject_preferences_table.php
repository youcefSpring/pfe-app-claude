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
        Schema::create('team_subject_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->unsignedTinyInteger('preference_order');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['team_id', 'subject_id']);
            $table->index('preference_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_subject_preferences');
    }
};
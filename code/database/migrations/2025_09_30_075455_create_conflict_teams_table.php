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
        Schema::create('conflict_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conflict_id')->constrained('subject_conflicts')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->integer('priority_score')->default(0);
            $table->timestamp('selection_date');
            $table->timestamps();

            $table->unique(['conflict_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conflict_teams');
    }
};

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
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('preference_order')->comment('1-10, where 1 is most preferred');
            $table->timestamp('selected_at')->nullable();
            $table->foreignId('selected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_allocated')->default(false);
            $table->timestamps();

            $table->unique(['team_id', 'subject_id']);
            $table->unique(['team_id', 'preference_order']);
            $table->index(['team_id', 'preference_order']);
            $table->index('is_allocated');
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

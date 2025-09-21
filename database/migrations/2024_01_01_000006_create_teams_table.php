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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->foreignId('leader_id')->constrained('users');
            $table->unsignedTinyInteger('size');
            $table->enum('status', ['forming', 'complete', 'validated', 'assigned'])->default('forming');
            $table->timestamp('formation_completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('leader_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
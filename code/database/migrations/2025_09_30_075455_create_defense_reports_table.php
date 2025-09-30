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
        Schema::create('defense_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_id')->constrained('defenses')->onDelete('cascade');
            $table->string('file_path', 500);
            $table->timestamp('generated_at');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique('defense_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defense_reports');
    }
};

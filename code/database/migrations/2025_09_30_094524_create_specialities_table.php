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
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Ingénierie du logiciel et traitement de l'information"
            $table->string('code')->nullable(); // Optional speciality code
            $table->string('level')->default('license'); // license, master, doctorate
            $table->string('academic_year'); // e.g., "2024/2025"
            $table->string('semester')->nullable(); // e.g., "S1", "S2"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'level', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialities');
    }
};

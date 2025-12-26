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
        Schema::table('users', function (Blueprint $table) {
            // Check if columns don't already exist before adding them
            if (!Schema::hasColumn('users', 'speciality_id')) {
                $table->foreignId('speciality_id')->nullable()->constrained('specialities')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'numero_inscription')) {
                $table->string('numero_inscription')->nullable()->unique(); // Student registration number
            }
            if (!Schema::hasColumn('users', 'annee_bac')) {
                $table->year('annee_bac')->nullable(); // High school graduation year
            }
            if (!Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable(); // Birth date
            }
            if (!Schema::hasColumn('users', 'section')) {
                $table->string('section')->nullable(); // e.g., Section_1
            }
            if (!Schema::hasColumn('users', 'groupe')) {
                $table->string('groupe')->nullable(); // e.g., G_02
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['speciality_id']);
            $table->dropColumn([
                'speciality_id',
                'numero_inscription',
                'annee_bac',
                'date_naissance',
                'section',
                'groupe'
            ]);
        });
    }
};

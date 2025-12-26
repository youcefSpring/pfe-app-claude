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
        Schema::table('defenses', function (Blueprint $table) {
            $table->string('academic_year', 10)->after('final_grade');
            $table->enum('session', ['session_1', 'session_2'])->default('session_1')->after('academic_year');
            $table->index('academic_year');
            $table->index('session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defenses', function (Blueprint $table) {
            $table->dropIndex(['academic_year']);
            $table->dropIndex(['session']);
            $table->dropColumn(['academic_year', 'session']);
        });
    }
};
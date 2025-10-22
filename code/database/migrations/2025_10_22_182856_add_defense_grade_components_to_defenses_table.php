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
            $table->decimal('manuscript_grade', 4, 2)->nullable()->after('final_grade')->comment('Grade for manuscript (6/8)');
            $table->decimal('oral_grade', 4, 2)->nullable()->after('manuscript_grade')->comment('Grade for oral presentation (4/6)');
            $table->decimal('questions_grade', 4, 2)->nullable()->after('oral_grade')->comment('Grade for answering questions (5/6)');
            $table->decimal('realization_grade', 4, 2)->nullable()->after('questions_grade')->comment('Grade for realization (5/-)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defenses', function (Blueprint $table) {
            $table->dropColumn([
                'manuscript_grade',
                'oral_grade',
                'questions_grade',
                'realization_grade'
            ]);
        });
    }
};

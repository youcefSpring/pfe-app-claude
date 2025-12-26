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
        Schema::table('defense_juries', function (Blueprint $table) {
            // Add multiple defense grade components that should sum to 20
            $table->decimal('presentation_grade', 4, 2)->nullable()->after('individual_grade')->comment('Grade for presentation quality');
            $table->decimal('content_grade', 4, 2)->nullable()->after('presentation_grade')->comment('Grade for content/technical quality');
            $table->decimal('methodology_grade', 4, 2)->nullable()->after('content_grade')->comment('Grade for methodology');
            $table->decimal('innovation_grade', 4, 2)->nullable()->after('methodology_grade')->comment('Grade for innovation/originality');
            $table->decimal('questions_grade', 4, 2)->nullable()->after('innovation_grade')->comment('Grade for answering questions');

            // Add grade weights (should sum to 100)
            $table->decimal('presentation_weight', 5, 2)->default(20)->after('questions_grade');
            $table->decimal('content_weight', 5, 2)->default(20)->after('presentation_weight');
            $table->decimal('methodology_weight', 5, 2)->default(20)->after('content_weight');
            $table->decimal('innovation_weight', 5, 2)->default(20)->after('methodology_weight');
            $table->decimal('questions_weight', 5, 2)->default(20)->after('innovation_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defense_juries', function (Blueprint $table) {
            $table->dropColumn([
                'presentation_grade', 'content_grade', 'methodology_grade',
                'innovation_grade', 'questions_grade',
                'presentation_weight', 'content_weight', 'methodology_weight',
                'innovation_weight', 'questions_weight'
            ]);
        });
    }
};

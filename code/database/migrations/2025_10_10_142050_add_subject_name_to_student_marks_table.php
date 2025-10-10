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
        Schema::table('student_marks', function (Blueprint $table) {
            // Add subject_name column if it doesn't exist
            if (!Schema::hasColumn('student_marks', 'subject_name')) {
                $table->string('subject_name')->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropColumn('subject_name');
        });
    }
};

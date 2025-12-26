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
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'external_supervisor_id')) {
                $table->foreignId('external_supervisor_id')->nullable()->constrained('users')->onDelete('set null')->after('student_id');
            }
        });

        // Add index only if it doesn't exist
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasIndex('subjects', 'subjects_external_supervisor_id_index')) {
                $table->index(['external_supervisor_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'external_supervisor_id')) {
                $table->dropForeign(['external_supervisor_id']);
                $table->dropIndex(['external_supervisor_id']);
                $table->dropColumn('external_supervisor_id');
            }
        });
    }
};

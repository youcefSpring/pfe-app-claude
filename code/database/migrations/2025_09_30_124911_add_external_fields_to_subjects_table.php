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
            if (!Schema::hasColumn('subjects', 'is_external')) {
                $table->boolean('is_external')->default(false)->after('status');
            }
            if (!Schema::hasColumn('subjects', 'company_name')) {
                $table->string('company_name')->nullable()->after('is_external');
            }
            if (!Schema::hasColumn('subjects', 'dataset_resources_link')) {
                $table->text('dataset_resources_link')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('subjects', 'student_id')) {
                $table->foreignId('student_id')->nullable()->constrained('users')->onDelete('cascade')->after('dataset_resources_link');
            }
        });

        // Add indexes only if they don't exist
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasIndex('subjects', 'subjects_is_external_index')) {
                $table->index(['is_external']);
            }
            if (!Schema::hasIndex('subjects', 'subjects_student_id_index')) {
                $table->index(['student_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['is_external']);
            $table->dropColumn(['is_external', 'company_name', 'dataset_resources_link', 'student_id']);
        });
    }
};

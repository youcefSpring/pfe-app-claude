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
            $table->unsignedBigInteger('subject_id')->nullable()->after('project_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            // Make project_id nullable since we're transitioning away from projects
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defenses', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');

            // Restore project_id as non-nullable
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }
};

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
            $table->unsignedBigInteger('scheduled_by')->nullable()->after('notes');
            $table->timestamp('scheduled_at')->nullable()->after('scheduled_by');

            $table->foreign('scheduled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defenses', function (Blueprint $table) {
            $table->dropForeign(['scheduled_by']);
            $table->dropColumn(['scheduled_by', 'scheduled_at']);
        });
    }
};

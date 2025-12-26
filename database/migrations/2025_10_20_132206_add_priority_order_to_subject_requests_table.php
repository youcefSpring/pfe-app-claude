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
        Schema::table('subject_requests', function (Blueprint $table) {
            $table->integer('priority_order')->default(0)->after('status');
            $table->index(['team_id', 'priority_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_requests', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'priority_order']);
            $table->dropColumn('priority_order');
        });
    }
};

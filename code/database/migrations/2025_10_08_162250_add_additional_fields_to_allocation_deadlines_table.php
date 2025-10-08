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
        Schema::table('allocation_deadlines', function (Blueprint $table) {
            $table->datetime('second_round_start')->nullable()->after('allocation_date');
            $table->datetime('second_round_deadline')->nullable()->after('second_round_start');
            $table->datetime('defense_scheduling_allowed_after')->nullable()->after('second_round_deadline');
            $table->boolean('auto_allocation_completed')->default(false)->after('defense_scheduling_allowed_after');
            $table->boolean('second_round_needed')->default(false)->after('auto_allocation_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allocation_deadlines', function (Blueprint $table) {
            $table->dropColumn([
                'second_round_start',
                'second_round_deadline',
                'defense_scheduling_allowed_after',
                'auto_allocation_completed',
                'second_round_needed'
            ]);
        });
    }
};

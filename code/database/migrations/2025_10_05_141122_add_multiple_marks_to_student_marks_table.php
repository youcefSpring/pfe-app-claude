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
            // Add individual mark fields
            $table->decimal('mark_1', 5, 2)->nullable()->after('mark');
            $table->decimal('mark_2', 5, 2)->nullable()->after('mark_1');
            $table->decimal('mark_3', 5, 2)->nullable()->after('mark_2');
            $table->decimal('mark_4', 5, 2)->nullable()->after('mark_3');
            $table->decimal('mark_5', 5, 2)->nullable()->after('mark_4');

            // Add max marks for each component
            $table->decimal('max_mark_1', 5, 2)->default(20.00)->after('mark_5');
            $table->decimal('max_mark_2', 5, 2)->default(20.00)->after('max_mark_1');
            $table->decimal('max_mark_3', 5, 2)->default(20.00)->after('max_mark_2');
            $table->decimal('max_mark_4', 5, 2)->default(20.00)->after('max_mark_3');
            $table->decimal('max_mark_5', 5, 2)->default(20.00)->after('max_mark_4');

            // Add weights for each component (percentage of final grade)
            $table->decimal('weight_1', 5, 2)->default(20.00)->after('max_mark_5');
            $table->decimal('weight_2', 5, 2)->default(20.00)->after('weight_1');
            $table->decimal('weight_3', 5, 2)->default(20.00)->after('weight_2');
            $table->decimal('weight_4', 5, 2)->default(20.00)->after('weight_3');
            $table->decimal('weight_5', 5, 2)->default(20.00)->after('weight_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropColumn([
                'mark_1', 'mark_2', 'mark_3', 'mark_4', 'mark_5',
                'max_mark_1', 'max_mark_2', 'max_mark_3', 'max_mark_4', 'max_mark_5',
                'weight_1', 'weight_2', 'weight_3', 'weight_4', 'weight_5'
            ]);
        });
    }
};

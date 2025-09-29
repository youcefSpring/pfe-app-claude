<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add Laravel notification system columns
            $table->uuid('uuid')->after('id')->nullable();
            $table->string('notifiable_type')->after('user_id');
            $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');

            // Add index for Laravel notification system
            $table->index(['notifiable_type', 'notifiable_id']);
        });

        // Update existing records to be compatible with Laravel notifications
        DB::statement("UPDATE notifications SET notifiable_type = 'App\\\\Models\\\\User', notifiable_id = user_id WHERE notifiable_type IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['uuid', 'notifiable_type', 'notifiable_id']);
        });
    }
};

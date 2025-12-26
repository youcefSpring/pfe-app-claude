<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix cascade delete rules to prevent accidental data loss.
     * - projects.supervisor_id: CASCADE → SET NULL (preserve project history)
     * - defenses.room_id: CASCADE → SET NULL (preserve defense records)
     */
    public function up(): void
    {
        // Get database name for foreign key lookup
        $databaseName = \DB::getDatabaseName();

        // Fix projects.supervisor_id foreign key
        $projectsFk = \DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = 'projects'
            AND COLUMN_NAME = 'supervisor_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$databaseName]);

        if ($projectsFk) {
            Schema::table('projects', function (Blueprint $table) use ($projectsFk) {
                // Drop existing foreign key
                $table->dropForeign($projectsFk->CONSTRAINT_NAME);
            });
        }

        Schema::table('projects', function (Blueprint $table) {
            // Make supervisor_id nullable (required for SET NULL)
            $table->unsignedBigInteger('supervisor_id')->nullable()->change();

            // Recreate with SET NULL instead of CASCADE
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // Fix defenses.room_id foreign key
        $defensesFk = \DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = 'defenses'
            AND COLUMN_NAME = 'room_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$databaseName]);

        if ($defensesFk) {
            Schema::table('defenses', function (Blueprint $table) use ($defensesFk) {
                // Drop existing foreign key
                $table->dropForeign($defensesFk->CONSTRAINT_NAME);
            });
        }

        Schema::table('defenses', function (Blueprint $table) {
            // Make room_id nullable (for better flexibility)
            $table->unsignedBigInteger('room_id')->nullable()->change();

            // Recreate with SET NULL instead of CASCADE
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the SET NULL foreign key
            $table->dropForeign(['supervisor_id']);

            // Make supervisor_id NOT NULL again
            $table->foreignId('supervisor_id')->nullable(false)->change();

            // Restore CASCADE behavior
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('defenses', function (Blueprint $table) {
            // Drop the SET NULL foreign key
            $table->dropForeign(['room_id']);

            // Make room_id NOT NULL again
            $table->foreignId('room_id')->nullable(false)->change();

            // Restore CASCADE behavior
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');
        });
    }
};

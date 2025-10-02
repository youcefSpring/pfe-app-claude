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
        Schema::table('users', function (Blueprint $table) {
            // Add only missing columns that don't exist yet
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('name'); // Prénom
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name', 100)->nullable()->after('first_name'); // Nom
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->after('role'); // active, inactive, suspended, etc.
            }
            if (!Schema::hasColumn('users', 'academic_year')) {
                $table->string('academic_year', 20)->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'profile_completed')) {
                $table->boolean('profile_completed')->default(false)->after('academic_year');
            }
        });

        // Add indexes separately to avoid conflicts
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('numero_inscription');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('section');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('groupe');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns that were added in this migration
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
            if (Schema::hasColumn('users', 'profile_completed')) {
                $table->dropColumn('profile_completed');
            }
        });
    }
};

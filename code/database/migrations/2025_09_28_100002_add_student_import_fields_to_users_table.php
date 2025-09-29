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
            // Academic information
            $table->string('academic_year', 10)->nullable();
            $table->string('specialization')->nullable();

            // Personal details
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();

            // Background and profile
            $table->text('previous_education')->nullable();
            $table->json('skills')->nullable();
            $table->json('interests')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_picture')->nullable();
            $table->json('contact_info')->nullable();
            $table->string('cv_file_path')->nullable();

            // Import tracking
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('imported_at')->nullable();
            $table->unsignedBigInteger('imported_by')->nullable();
            $table->boolean('updated_via_import')->default(false);
            $table->timestamp('last_import_update')->nullable();

            // Add foreign key for imported_by
            $table->foreign('imported_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['imported_by']);

            // Drop all added columns
            $table->dropColumn([
                'academic_year',
                'specialization',
                'date_of_birth',
                'address',
                'emergency_contact',
                'previous_education',
                'skills',
                'interests',
                'bio',
                'profile_picture',
                'contact_info',
                'cv_file_path',
                'must_change_password',
                'imported_at',
                'imported_by',
                'updated_via_import',
                'last_import_update'
            ]);
        });
    }
};

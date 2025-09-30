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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('password');
            $table->string('matricule', 50)->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('speciality', 100)->nullable();
            $table->enum('grade', ['master', 'phd', 'professor'])->default('master');
            $table->enum('role', ['student', 'teacher', 'department_head', 'admin', 'external_supervisor'])->default('student');
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('matricule', 'idx_matricule');
            $table->index('email', 'idx_email');
            $table->index('department', 'idx_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

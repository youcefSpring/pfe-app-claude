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
            $table->string('birth_certificate_path')->nullable()->after('lieu_naissance');
            $table->enum('birth_certificate_status', ['pending', 'approved', 'rejected'])->default('pending')->after('birth_certificate_path');
            $table->text('birth_certificate_notes')->nullable()->after('birth_certificate_status');
            $table->timestamp('birth_certificate_approved_at')->nullable()->after('birth_certificate_notes');
            $table->unsignedBigInteger('birth_certificate_approved_by')->nullable()->after('birth_certificate_approved_at');
            $table->string('student_level')->nullable()->after('birth_certificate_approved_by'); // 'licence_3', 'master_1', 'master_2'

            $table->foreign('birth_certificate_approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['birth_certificate_approved_by']);
            $table->dropColumn([
                'birth_certificate_path',
                'birth_certificate_status',
                'birth_certificate_notes',
                'birth_certificate_approved_at',
                'birth_certificate_approved_by',
                'student_level'
            ]);
        });
    }
};

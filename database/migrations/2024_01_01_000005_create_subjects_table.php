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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description');
            $table->json('keywords');
            $table->json('required_tools')->nullable();
            $table->unsignedTinyInteger('max_teams')->default(1);
            $table->foreignId('supervisor_id')->constrained('users');
            $table->string('external_supervisor', 100)->nullable();
            $table->string('external_company', 100)->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'needs_correction', 'published'])->default('draft');
            $table->text('validation_notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('supervisor_id');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
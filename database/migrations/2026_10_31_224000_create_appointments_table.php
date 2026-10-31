<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number', 40)->unique();
            $table->foreignId('patient_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinical_encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_clinician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('reason', 500);
            $table->text('patient_instructions')->nullable();
            $table->string('status', 24)->default('scheduled');
            $table->text('outcome_notes')->nullable();
            $table->foreignId('scheduled_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('status_changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index(['patient_profile_id', 'scheduled_at']);
            $table->index(['assigned_clinician_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

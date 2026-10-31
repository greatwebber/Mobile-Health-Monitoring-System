<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinical_encounter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vital_sign_observation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('threshold_rule_id')->constrained()->restrictOnDelete();
            $table->string('metric', 48);
            $table->decimal('measured_value', 14, 4);
            $table->string('unit', 32);
            $table->string('severity', 24);
            $table->string('status', 24)->default('open');
            $table->string('rule_name', 160);
            $table->unsignedInteger('rule_version');
            $table->text('message');
            $table->timestamp('detected_at');
            $table->foreignId('acknowledged_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->unique(['vital_sign_observation_id', 'threshold_rule_id']);
            $table->index(['status', 'severity', 'detected_at']);
            $table->index(['patient_profile_id', 'detected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_alerts');
    }
};

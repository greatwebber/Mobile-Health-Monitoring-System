<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounter_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_encounter_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('severity', 24);
            $table->timestamp('started_at')->nullable();
            $table->string('duration', 120)->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 24)->default('clinical');
            $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['clinical_encounter_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounter_symptoms');
    }
};

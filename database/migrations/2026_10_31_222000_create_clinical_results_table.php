<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_encounter_id')->constrained()->cascadeOnDelete();
            $table->string('category', 32);
            $table->string('test_name', 160);
            $table->string('test_code', 40)->nullable();
            $table->decimal('value_numeric', 14, 4)->nullable();
            $table->string('value_text', 500)->nullable();
            $table->string('unit', 40)->nullable();
            $table->string('reference_range', 120)->nullable();
            $table->string('flag', 24)->default('not_flagged');
            $table->string('specimen', 120)->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('resulted_at');
            $table->text('notes')->nullable();
            $table->string('source', 24)->default('clinical');
            $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['clinical_encounter_id', 'resulted_at']);
            $table->index(['test_code', 'resulted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_results');
    }
};

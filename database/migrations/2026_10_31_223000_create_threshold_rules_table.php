<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threshold_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('metric', 48);
            $table->decimal('minimum_value', 14, 4)->nullable();
            $table->decimal('maximum_value', 14, 4)->nullable();
            $table->string('unit', 32);
            $table->string('severity', 24);
            $table->text('guidance');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('deactivated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();

            $table->index(['metric', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threshold_rules');
    }
};

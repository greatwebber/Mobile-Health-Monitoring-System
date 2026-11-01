<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_sync_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('draft_id');
            $table->string('resource_type', 80);
            $table->unsignedBigInteger('resource_id');
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['user_id', 'draft_id']);
            $table->index(['resource_type', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_sync_receipts');
    }
};

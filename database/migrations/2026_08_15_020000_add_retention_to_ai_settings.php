<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('retention_days')->default(30)->after('history_limit');
            $table->boolean('save_guest_conversations')->default(false)->after('retention_days');
        });

        Schema::table('ai_conversations', function (Blueprint $table): void {
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table): void {
            $table->dropIndex(['updated_at']);
        });

        Schema::table('ai_settings', function (Blueprint $table): void {
            $table->dropColumn(['retention_days', 'save_guest_conversations']);
        });
    }
};

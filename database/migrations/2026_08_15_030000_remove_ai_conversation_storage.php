<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');

        Schema::table('ai_settings', function (Blueprint $table): void {
            $table->dropColumn(['retention_days', 'save_guest_conversations']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('retention_days')->default(30)->after('history_limit');
            $table->boolean('save_guest_conversations')->default(false)->after('retention_days');
        });

        Schema::create('ai_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('guest_token')->nullable()->index();
            $table->string('title')->nullable();
            $table->timestamps();
            $table->index('updated_at');
        });

        Schema::create('ai_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->text('content');
            $table->timestamps();
        });
    }
};

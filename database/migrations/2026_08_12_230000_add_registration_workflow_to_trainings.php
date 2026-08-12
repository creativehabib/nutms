<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_user', function (Blueprint $table): void {
            $table->string('status')->default('Pending')->after('user_id')->index();
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('completed_at')->nullable()->after('approved_at');
            $table->string('certificate_number')->nullable()->unique()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('training_user', function (Blueprint $table): void {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'completed_at', 'certificate_number']);
        });
    }
};

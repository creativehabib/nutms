<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('teacher')->index();
            $table->foreignId('college_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::table('colleges', function (Blueprint $table): void {
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_status')->default('approved')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });
        Schema::table('teachers', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('approval_status')->default('approved')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });

        DB::table('users')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('teachers', fn (Blueprint $table) => $table->dropConstrainedForeignId('approved_by'));
        Schema::table('teachers', fn (Blueprint $table) => $table->dropConstrainedForeignId('user_id'));
        Schema::table('teachers', fn (Blueprint $table) => $table->dropColumn(['approval_status', 'approved_at']));
        Schema::table('colleges', fn (Blueprint $table) => $table->dropConstrainedForeignId('approved_by'));
        Schema::table('colleges', fn (Blueprint $table) => $table->dropConstrainedForeignId('submitted_by'));
        Schema::table('colleges', fn (Blueprint $table) => $table->dropColumn(['approval_status', 'approved_at']));
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('teacher_id'));
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('college_id'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};

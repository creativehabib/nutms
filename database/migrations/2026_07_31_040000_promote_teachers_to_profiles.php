<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('teacher_id');
        });

        Schema::rename('teachers', 'teacher_profiles');
    }

    public function down(): void
    {
        Schema::rename('teacher_profiles', 'teachers');

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};

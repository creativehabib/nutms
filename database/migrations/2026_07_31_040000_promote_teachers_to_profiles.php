<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'teacher_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('teacher_id');
            });
        }

        if (Schema::hasTable('teachers') && ! Schema::hasTable('teacher_profiles')) {
            Schema::rename('teachers', 'teacher_profiles');
        }
    }

    public function down(): void
    {
    }
};

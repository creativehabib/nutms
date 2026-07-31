<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teacher_profiles') && ! Schema::hasColumn('teacher_profiles', 'bank_account_number')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->string('bank_account_number', 100)->nullable()->after('bank_branch_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teacher_profiles') && Schema::hasColumn('teacher_profiles', 'bank_account_number')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->dropColumn('bank_account_number');
            });
        }
    }
};

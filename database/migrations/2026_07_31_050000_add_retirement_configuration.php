<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('teacher_profiles', 'birth_date')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->date('birth_date')->nullable()->after('name')->index();
            });
        }

        if (! Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->string('value');
                $table->timestamps();
            });
        }

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'retirement_age'],
            ['value' => '59', 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function down(): void
    {
    }
};

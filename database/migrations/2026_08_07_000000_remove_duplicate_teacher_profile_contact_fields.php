<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = collect(['tmis_id', 'mobile_number', 'email'])
            ->filter(fn (string $column): bool => Schema::hasColumn('teacher_profiles', $column))
            ->values()
            ->all();

        if ($columns === []) {
            return;
        }

        Schema::table('teacher_profiles', function (Blueprint $table) use ($columns): void {
            $table->dropColumn($columns);
        });
    }

    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table): void {
            if (! Schema::hasColumn('teacher_profiles', 'tmis_id')) {
                $table->string('tmis_id')->nullable()->unique()->after('college_name');
            }

            if (! Schema::hasColumn('teacher_profiles', 'mobile_number')) {
                $table->string('mobile_number')->nullable()->after('permanent_address');
            }

            if (! Schema::hasColumn('teacher_profiles', 'email')) {
                $table->string('email')->nullable()->after('mobile_number');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = $this->teacherTableName();

        if ($tableName === null) {
            return;
        }

        $columns = collect([
            'has_training',
            'ict_training_duration',
            'other_training_duration',
            'training_year',
        ])->filter(fn (string $column): bool => Schema::hasColumn($tableName, $column))->all();

        if ($columns === []) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($columns): void {
            $table->dropColumn($columns);
        });
    }

    private function teacherTableName(): ?string
    {
        if (Schema::hasTable('teacher_profiles')) {
            return 'teacher_profiles';
        }

        if (Schema::hasTable('teachers')) {
            return 'teachers';
        }

        return null;
    }

    public function down(): void
    {
    }
};

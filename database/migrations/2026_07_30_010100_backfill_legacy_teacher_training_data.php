<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('teachers')->where(function ($query): void {
            $query->whereNotNull('ict_training_name')->orWhereNotNull('other_training_name');
        })->orderBy('id')->each(function (object $teacher): void {
            $this->backfillTraining($teacher, 'ict_training_name', 'ict_training_duration');
            $this->backfillTraining($teacher, 'other_training_name', 'other_training_duration');
        });
    }

    private function backfillTraining(object $teacher, string $nameColumn, string $durationColumn): void
    {
        $name = trim((string) $teacher->{$nameColumn});
        $year = filter_var($teacher->training_year, FILTER_VALIDATE_INT);

        if ($name === '' || $year === false || $year < 1900 || $year > 2100) {
            return;
        }

        $instituteName = trim((string) $teacher->training_institute) ?: 'অনির্ধারিত প্রতিষ্ঠান';
        $instituteId = DB::table('training_institutes')->where('name', $instituteName)->value('id')
            ?? DB::table('training_institutes')->insertGetId(['name' => $instituteName, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

        $trainingTypeId = DB::table('training_types')->where('training_institute_id', $instituteId)->where('name', $name)->value('id');
        if ($trainingTypeId === null) {
            [$durationValue, $durationUnit] = $this->parseDuration((string) $teacher->{$durationColumn});
            $trainingTypeId = DB::table('training_types')->insertGetId([
                'training_institute_id' => $instituteId,
                'name' => $name,
                'duration_value' => $durationValue,
                'duration_unit' => $durationUnit,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('teacher_training')->insertOrIgnore([
            'teacher_id' => $teacher->id,
            'training_type_id' => $trainingTypeId,
            'training_year' => $year,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @return array{0: int|null, 1: string|null} */
    private function parseDuration(string $duration): array
    {
        if (preg_match('/(\d+)\s*(hour|day|week|month|ঘণ্টা|দিন|সপ্তাহ|মাস)/iu', $duration, $matches) !== 1) {
            return [null, null];
        }

        $units = ['hour' => 'hours', 'ঘণ্টা' => 'hours', 'day' => 'days', 'দিন' => 'days', 'week' => 'weeks', 'সপ্তাহ' => 'weeks', 'month' => 'months', 'মাস' => 'months'];

        return [(int) $matches[1], $units[mb_strtolower($matches[2])] ?? null];
    }

    public function down(): void
    {
        // Intentionally left empty so a rollback never deletes training data entered after deployment.
    }
};

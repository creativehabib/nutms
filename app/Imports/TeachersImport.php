<?php

namespace App\Imports;

use App\Models\College;
use App\Models\Designation;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherOtherTraining;
use App\Models\TeacherLevel;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TeachersImport implements ToCollection, WithStartRow, WithChunkReading
{
    protected $collegeName;

    // কনস্ট্রাক্টরের মাধ্যমে ফাইলের নাম থেকে পাওয়া কলেজের নাম রিসিভ করা হচ্ছে
    public function __construct($collegeName = null)
    {
        $this->collegeName = $collegeName;
    }

    public function startRow(): int
    {
        return 2; // হেডিং স্কিপ করার জন্য
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // ফাইলের ধরন অনুযায়ী কলামের ইনডেক্স ডাইনামিক্যালি বের করার লজিক
            $offset = 0;

            // আমরা চেক করব মোবাইল ও ইমেইলের ডেটা ১৮ নাকি ১৯ নম্বর ইনডেক্সে আছে
            $contactStr18 = (string) ($row[18] ?? '');
            $contactStr19 = (string) ($row[19] ?? '');

            if (str_contains($contactStr19, '@') || preg_match('/[0-9]{11}/', $contactStr19)) {
                $offset = 1; // আগের ফাইলের মতো যদি ১ ঘর সরে থাকে
            } elseif (str_contains($contactStr18, '@') || preg_match('/[0-9]{11}/', $contactStr18)) {
                $offset = 0; // নতুন ফাইলের মতো যদি শুরু থেকেই থাকে
            } else {
                // কোনো কারণে ইমেইল না থাকলে প্রথম কলাম ফাঁকা কি না তা দেখে যাচাই করা
                $offset = empty($row[0]) ? 1 : 0;
            }

            // ডাইনামিক অফসেট (offset) দিয়ে ডেটা বের করা
            $name   = $row[4 + $offset] ?? null;

            if (!$name) {
                continue; // ফাঁকা রো স্কিপ করে লুপের পরের লাইনে চলে যাবে
            }

            $data = [
                'college_code'            => trim((string) ($row[0 + $offset] ?? '')),
                'ttis_id'                 => $row[3 + $offset] ?? null,
                'name'                    => $name,
                'designation'             => $row[5 + $offset] ?? null,
                'subject'                 => $row[6 + $offset] ?? null,
                'teacher_level'           => $row[7 + $offset] ?? null,
                'employment_type'         => $row[8 + $offset] ?? null,
                'ict_training_name'       => $row[10 + $offset] ?? null,
                'ict_training_duration'   => $row[11 + $offset] ?? null,
                'other_training_name'     => $row[12 + $offset] ?? null,
                'other_training_duration' => $row[13 + $offset] ?? null,
                'training_institute'      => $row[14 + $offset] ?? null,
            ];

            $collegeName = $this->collegeName ?: $data['college_code'] ?: 'অনির্ধারিত কলেজ';
            $college = College::query()->firstOrCreate(
                $data['college_code'] !== '' ? ['college_code' => $data['college_code']] : ['name' => $collegeName],
                ['name' => $collegeName],
            );

            $teacherData = collect($data)->except([
                'college_code',
                'designation',
                'subject',
                'teacher_level',
                'employment_type',
                'ict_training_name',
                'ict_training_duration',
                'other_training_name',
                'other_training_duration',
                'training_institute',
            ])->merge([
                'college_id' => $college->id,
                'designation_id' => filled($data['designation']) ? Designation::query()->firstOrCreate(['name' => $data['designation']])->id : null,
                'subject_id' => filled($data['subject']) ? Subject::query()->firstOrCreate(['name' => $data['subject']])->id : null,
                'teacher_level_id' => filled($data['teacher_level']) ? TeacherLevel::query()->firstOrCreate(['name' => $data['teacher_level']])->id : null,
                'employment_id' => filled($data['employment_type']) ? Employment::query()->firstOrCreate(['name' => $data['employment_type']])->id : null,
            ])->all();

            // ডেটা সেভ বা আপডেট করা
            $teacherIdentity = filled($data['ttis_id'])
                ? ['ttis_id' => $data['ttis_id']]
                : [
                    'name' => $name,
                    'subject_id' => $teacherData['subject_id'],
                    'college_id' => $college->id,
                ];

            $teacher = Teacher::updateOrCreate($teacherIdentity, $teacherData);

            $data['training_year'] = $row[15 + $offset] ?? null;
            $this->synchronizeTraining($teacher, $data);
        }
    }

    // মেমরি ও স্পিড অপটিমাইজেশনের জন্য Chunk যুক্ত করা হলো
    public function chunkSize(): int
    {
        return 500;
    }

    /** @param array<string, mixed> $data */
    private function synchronizeTraining(Teacher $teacher, array $data): void
    {
        $year = filter_var($data['training_year'], FILTER_VALIDATE_INT);
        if ($year === false || $year < 1900 || $year > ((int) date('Y') + 1)) {
            return;
        }

        $instituteName = trim((string) $data['training_institute']) ?: 'অনির্ধারিত প্রতিষ্ঠান';
        $institute = TrainingInstitute::query()->firstOrCreate(['name' => $instituteName]);

        $trainingName = trim((string) $data['ict_training_name']);
        if ($trainingName !== '') {
            [$durationValue, $durationUnit] = $this->parseDuration((string) $data['ict_training_duration']);
            $trainingType = TrainingType::query()->firstOrCreate(
                ['training_institute_id' => $institute->id, 'name' => $trainingName],
                ['duration_value' => $durationValue, 'duration_unit' => $durationUnit],
            );
            $alreadyLinked = $teacher->trainingTypes()->whereKey($trainingType->id)
                ->wherePivot('training_year', $year)->exists();
            if (! $alreadyLinked) {
                $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => $year]);
            }
        }

        $otherTrainingName = trim((string) $data['other_training_name']);
        if ($otherTrainingName !== '') {
            [$durationValue, $durationUnit] = $this->parseDuration((string) $data['other_training_duration']);
            TeacherOtherTraining::query()->firstOrCreate([
                'teacher_id' => $teacher->id,
                'name' => $otherTrainingName,
                'training_year' => $year,
            ], [
                'training_institute_id' => $institute->id,
                'duration_value' => $durationValue,
                'duration_unit' => $durationUnit,
            ]);
        }
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
}

<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\Designation;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

class TeacherDataSeeder extends Seeder
{
    /** @var array<string, string> */
    private const SUBJECT_ALIASES = [
        'ANTHROPOLOG Y' => 'Anthropology',
        'B.ED' => 'Education (B.Ed)',
        'BBA' => 'Business Administration (BBA)',
        'BFA' => 'Fine Arts (BFA)',
        'CSE' => 'Computer Science and Engineering (CSE)',
        'EDUCATION' => 'Education (B.Ed)',
        'ENVIRONMEAN TAL SCIENCE' => 'Environmental Science',
        'GEOGRAPHY AND' => 'Geography and Environment',
        'LIBRARY & INFORMATION' => 'Library & Information Science',
        'M.ED' => 'Education (B.Ed)',
        'PUBLIC ADMINISTRATI ON' => 'Public Administration',
        'TOURISM AND HOSPITALITY' => 'Tourism And Hospitality Management',
    ];

    public function __construct(private ?string $sourcePath = null)
    {
    }

    public function run(): void
    {
        $sourcePath = $this->sourcePath ?? database_path('teacher_data.xlsx');

        if (! is_file($sourcePath)) {
            throw new RuntimeException("Teacher data file does not exist: {$sourcePath}");
        }

        $this->call([
            SubjectSeeder::class,
            DesignationSeeder::class,
            TeacherLevelSeeder::class,
        ]);

        $spreadsheet = IOFactory::load($sourcePath);

        $rows = collect($spreadsheet->getActiveSheet()->toArray(null, true, true, true))
            ->skip(1)
            ->filter(fn (array $row): bool => filled($row['D'] ?? null));

        $this->seedRows($rows);
    }

    /** @param Collection<int, array<string, mixed>> $rows */
    private function seedRows(Collection $rows): void
    {
        $subjects = Subject::query()->get()->keyBy(fn (Subject $subject): string => Str::upper($subject->name));
        $designations = Designation::query()->get()->keyBy(fn (Designation $designation): string => Str::upper($designation->name));
        $teacherLevels = TeacherLevel::query()->get()->keyBy(fn (TeacherLevel $teacherLevel): string => Str::upper($teacherLevel->name));
        $colleges = College::query()->get()->keyBy(fn (College $college): string => $this->normalizeCollegeCode($college->college_code));
        $existingTtisIds = Teacher::withTrashed()
            ->pluck('ttis_id')
            ->filter(fn (mixed $ttisId): bool => filled($ttisId))
            ->mapWithKeys(fn (string $ttisId): array => [$ttisId => true])
            ->all();
        $existingTeachersByImportKey = Teacher::withTrashed()
            ->with('user:id,email,mobile_no')
            ->get(['id', 'user_id', 'college_id', 'ttis_id', 'name'])
            ->mapWithKeys(fn (Teacher $teacher): array => [
                $this->teacherImportKey(
                    (int) $teacher->college_id,
                    $teacher->name,
                    $teacher->user?->email,
                    $teacher->user?->mobile_no,
                ) => $teacher,
            ]);
        $usedEmails = User::query()->pluck('email')->mapWithKeys(fn (string $email): array => [Str::lower($email) => true])->all();
        $usedMobiles = User::query()->pluck('mobile_no')->mapWithKeys(fn (string $mobile): array => [$mobile => true])->all();
        $password = Hash::make('12345678');
        $skippedRows = 0;
        $skippedExistingRows = 0;
        $seededRows = 0;
        $processedRows = 0;
        $updatedTtisRows = 0;

        $rows->chunk(250)->each(function (Collection $chunk) use ($subjects, $designations, $teacherLevels, $colleges, &$existingTtisIds, $existingTeachersByImportKey, &$usedEmails, &$usedMobiles, $password, &$skippedRows, &$skippedExistingRows, &$seededRows, &$processedRows, &$updatedTtisRows, $rows): void {
            foreach ($chunk as $row) {
                $ttisId = trim((string) $row['E']);

                if ($ttisId !== '' && isset($existingTtisIds[$ttisId])) {
                    $skippedExistingRows++;

                    continue;
                }

                $college = $colleges->get($this->normalizeCollegeCode($row['A'] ?? null));

                if (! $college instanceof College) {
                    $skippedRows++;

                    continue;
                }

                $collegeType = $this->normalizeCollegeType($row['C'] ?? null);

                if ($collegeType !== null && $college->college_type !== $collegeType) {
                    $college->update(['college_type' => $collegeType]);
                }

                $importKey = $this->teacherImportKey($college->id, $row['D'] ?? null, $row['J'] ?? null, $row['I'] ?? null);
                $existingImportedTeacher = $existingTeachersByImportKey->get($importKey);

                if ($ttisId === '' && $existingImportedTeacher instanceof Teacher) {
                    $skippedExistingRows++;

                    continue;
                }

                if ($ttisId !== '' && $existingImportedTeacher instanceof Teacher && blank($existingImportedTeacher->ttis_id)) {
                    $existingImportedTeacher->update(['ttis_id' => $ttisId]);
                    $existingTtisIds[$ttisId] = true;
                    $updatedTtisRows++;

                    continue;
                }

                $accountIdentifier = $ttisId !== '' ? $ttisId : substr(hash('sha256', $importKey), 0, 12);
                $email = $this->uniqueEmail((string) ($row['J'] ?? ''), $accountIdentifier, $usedEmails);
                $mobile = $this->uniqueMobile((string) ($row['I'] ?? ''), $accountIdentifier, $usedMobiles);
                $name = $this->normalizeTeacherName($row['D'] ?? null, $ttisId);

                $user = User::query()->updateOrCreate(['email' => $email], [
                    'name' => $name,
                    'mobile_no' => $mobile,
                    'password' => $password,
                    'college_id' => $college->id,
                    'email_verified_at' => now(),
                    'approval_status' => ApprovalStatus::Approved,
                    'approved_at' => now(),
                ]);
                $user->syncRoles([Str::lower(trim((string) ($row['K'] ?? ''))) === 'principal' ? 'principal' : 'teacher']);

                $subjectName = self::SUBJECT_ALIASES[Str::upper(trim((string) ($row['G'] ?? '')))]
                    ?? trim((string) ($row['G'] ?? ''));

                $teacherAttributes = [
                    'user_id' => $user->id,
                    'college_id' => $college->id,
                    'name' => $name,
                    'designation_id' => $designations->get(Str::upper(trim((string) ($row['F'] ?? ''))))?->id,
                    'subject_id' => $subjects->get(Str::upper($subjectName))?->id,
                    'teacher_level_id' => $teacherLevels->get(Str::upper(trim((string) ($row['L'] ?? ''))))?->id,
                    'birth_date' => filled($row['H'] ?? null) ? $row['H'] : null,
                    'approval_status' => ApprovalStatus::Approved,
                    'approved_at' => now(),
                ];
                $teacher = $ttisId === ''
                    ? Teacher::withoutEvents(fn (): Teacher => Teacher::query()->create([...$teacherAttributes, 'ttis_id' => null]))
                    : Teacher::withTrashed()->updateOrCreate(['ttis_id' => $ttisId], $teacherAttributes);
                $teacher->restore();

                if ($ttisId !== '') {
                    $existingTtisIds[$ttisId] = true;
                }

                $existingTeachersByImportKey->put($importKey, $teacher);
                $seededRows++;
            }

            $processedRows += $chunk->count();
            $this->command?->info("Processed {$processedRows}/{$rows->count()} teacher rows; seeded {$seededRows}, TTIS updated {$updatedTtisRows}, skipped existing {$skippedExistingRows}, missing college {$skippedRows}.");
        });

        if ($skippedRows > 0) {
            $this->command?->warn("Skipped {$skippedRows} teacher rows because their college codes do not exist.");
        }

        $this->command?->info("Teacher seeding complete: {$seededRows} seeded, {$updatedTtisRows} TTIS IDs updated, {$skippedExistingRows} already existed, {$skippedRows} skipped for missing colleges.");
    }

    private function teacherImportKey(int $collegeId, mixed $name, mixed $email, mixed $mobile): string
    {
        $normalizedEmail = Str::lower(trim((string) $email));
        $normalizedMobile = preg_replace('/\D+/', '', (string) $mobile) ?? '';
        $identity = $normalizedEmail !== '' || $normalizedMobile !== ''
            ? "{$normalizedEmail}|{$normalizedMobile}"
            : Str::lower(Str::squish((string) $name));

        return implode('|', [
            $collegeId,
            $identity,
        ]);
    }

    private function normalizeCollegeCode(mixed $collegeCode): string
    {
        $normalizedCode = ltrim(trim((string) $collegeCode), '0');

        return $normalizedCode === '' ? '0' : $normalizedCode;
    }

    private function normalizeCollegeType(mixed $collegeType): ?string
    {
        return match (Str::lower(trim((string) $collegeType))) {
            'govt', 'government' => 'government',
            'non-govt', 'non government', 'non-government', 'private' => 'private',
            default => null,
        };
    }

    private function normalizeTeacherName(mixed $name, string $ttisId): string
    {
        $name = Str::of((string) $name)->squish()->toString();

        if (
            $name === ''
            || Str::length($name) > 255
            || Str::contains($name, ['Teachers Training Information System', 'College Wise Principal/Vice Principal/Teacher Report'])
        ) {
            return "Teacher {$ttisId}";
        }

        return $name;
    }

    /** @param array<string, bool> $usedEmails */
    private function uniqueEmail(string $email, string $ttisId, array &$usedEmails): string
    {
        $email = Str::lower(trim($email));
        $candidate = $email !== '' ? $email : "teacher-{$ttisId}@example.invalid";

        if (isset($usedEmails[$candidate])) {
            [$localPart, $domain] = str_contains($candidate, '@')
                ? explode('@', $candidate, 2)
                : [$candidate, 'example.invalid'];
            $candidate = "{$localPart}+{$ttisId}@{$domain}";

            for ($suffix = 2; isset($usedEmails[$candidate]); $suffix++) {
                $candidate = "{$localPart}+{$ttisId}-{$suffix}@{$domain}";
            }
        }

        $usedEmails[$candidate] = true;

        return $candidate;
    }

    /** @param array<string, bool> $usedMobiles */
    private function uniqueMobile(string $mobile, string $ttisId, array &$usedMobiles): string
    {
        $candidate = preg_replace('/\D+/', '', $mobile) ?? '';
        $candidate = Str::length($candidate) === 10 && Str::startsWith($candidate, '1') ? "0{$candidate}" : $candidate;
        $candidate = $candidate !== '' ? $candidate : "missing-{$ttisId}";

        if (isset($usedMobiles[$candidate])) {
            $baseCandidate = "{$candidate}-{$ttisId}";
            $candidate = $baseCandidate;

            for ($suffix = 2; isset($usedMobiles[$candidate]); $suffix++) {
                $candidate = "{$baseCandidate}-{$suffix}";
            }
        }

        $usedMobiles[$candidate] = true;

        return $candidate;
    }
}

<?php

use App\Models\College;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\TeacherDataSeeder;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('only seeds newly appended teacher profiles and login accounts on subsequent runs', function () {
    $sourcePath = tempnam(sys_get_temp_dir(), 'teachers-');
    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray([
        ['College Code', 'College Name', 'College Category', 'Name', 'TTIS ID', 'Designation', 'Subject', 'Date of Birth', 'Mobile', 'Email', 'Role', 'Category'],
        ['0101', 'Example College (101)', 'Govt', 'FIRST TEACHER', '17535', 'Professor', 'MANAGEMENT', '1961-12-18', '1712345678', 'shared@example.com', 'Principal', 'Honours'],
        ['0101', 'Example College (101)', 'Govt', 'MOHAMMAD REDUANUL HOQUE Lecturer BANGLA 39 y, 7 m, 24 d 12 year, 9 Non Priority 01832757466 reduan@example.com Teacher Degree NATIONAL UNIVERSITY GAZIPUR-1704, BANGLADESH Teachers Training Information System College Wise Principal/Vice Principal/Teacher Report '.str_repeat('invalid report data ', 10), '17536', 'Lecturer', 'BBA', '1980-01-02', '01812345678', 'shared@example.com', 'Teacher', 'Degree'],
        ['0101', 'Example College (101)', 'Govt', 'TEACHER WITHOUT TTIS', null, 'Lecturer', 'BANGLA', '1988-01-02', '01612345678', 'generated@example.com', 'Teacher', 'Degree'],
        ['9999', 'Missing College', 'Govt', 'SKIPPED TEACHER', '17537', 'Lecturer', 'BANGLA', '1985-01-02', '01912345678', 'skipped@example.com', 'Teacher', 'Degree'],
    ]);
    (new Xlsx($spreadsheet))->save($sourcePath);

    try {
        $college = College::query()->create([
            'college_code' => '101',
            'name' => 'Existing College Name',
            'college_type' => null,
        ]);
        $seeder = (new TeacherDataSeeder($sourcePath))->setContainer($this->app);
        $seeder->run();

        $principal = User::query()->where('email', 'shared@example.com')->firstOrFail();
        $principal->update(['name' => 'Manually Updated Name']);
        $spreadsheet->getActiveSheet()->fromArray([
            ['0101', 'Example College (101)', 'Govt', 'NEW TEACHER', '17538', 'Lecturer', 'BANGLA', '1990-01-02', '01987654321', 'new@example.com', 'Teacher', 'Degree'],
        ], null, 'A6');
        (new Xlsx($spreadsheet))->save($sourcePath);
        $seeder->run();
        $seeder->run();

        $principal->refresh();
        $teacher = User::query()->where('email', 'shared+17536@example.com')->firstOrFail();
        $newTeacher = User::query()->where('email', 'new@example.com')->firstOrFail();
        $teacherWithoutTtis = User::query()->where('email', 'generated@example.com')->firstOrFail()->teacherProfile;
        $firstProfile = Teacher::query()->where('ttis_id', '17535')->firstOrFail();
        $secondProfile = Teacher::query()->where('ttis_id', '17536')->firstOrFail();
        $sourceSpreadsheetTtisId = IOFactory::load($sourcePath)->getActiveSheet()->getCell('E4')->getValue();

        expect(Hash::check('12345678', $principal->password))->toBeTrue()
            ->and($principal->mobile_no)->toBe('01712345678')
            ->and($principal->name)->toBe('Manually Updated Name')
            ->and($principal->hasRole('principal'))->toBeTrue()
            ->and($teacher->hasRole('teacher'))->toBeTrue()
            ->and($newTeacher->hasRole('teacher'))->toBeTrue()
            ->and($teacherWithoutTtis->ttis_id)->toMatch('/^\d{4}$/')
            ->and($sourceSpreadsheetTtisId)->toBeNull()
            ->and($teacher->name)->toBe('Teacher 17536')
            ->and($firstProfile->college->college_code)->toBe('101')
            ->and($college->fresh()->name)->toBe('Existing College Name')
            ->and($college->fresh()->college_type)->toBe('government')
            ->and($firstProfile->designation->name)->toBe('Professor')
            ->and($firstProfile->subject->name)->toBe('Management')
            ->and($firstProfile->teacherLevel->name)->toBe('Honours')
            ->and($firstProfile->birth_date->toDateString())->toBe('1961-12-18')
            ->and($secondProfile->subject->name)->toBe('Business Administration (BBA)')
            ->and($secondProfile->teacherLevel->name)->toBe('Degree')
            ->and(Teacher::query()->count())->toBe(4)
            ->and(User::query()->count())->toBe(4)
            ->and(College::query()->count())->toBe(1)
            ->and(User::query()->where('email', 'skipped@example.com')->doesntExist())->toBeTrue();
    } finally {
        if (is_file($sourcePath)) {
            unlink($sourcePath);
        }
    }
});

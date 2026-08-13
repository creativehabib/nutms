<?php

use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\TeacherDataSeeder;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('seeds teacher profiles and login accounts from the teacher spreadsheet', function () {
    $sourcePath = tempnam(sys_get_temp_dir(), 'teachers-');
    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray([
        ['College Code', 'College Name', 'College Category', 'Name', 'TTIS ID', 'Designation', 'Subject', 'Date of Birth', 'Mobile', 'Email', 'Role', 'Category'],
        ['0101', 'Example College (101)', 'Govt', 'FIRST TEACHER', '17535', 'Professor', 'MANAGEMENT', '1961-12-18', '1712345678', 'shared@example.com', 'Principal', 'Honours'],
        ['0101', 'Example College (101)', 'Govt', 'SECOND TEACHER', '17536', 'Lecturer', 'BBA', '1980-01-02', '01812345678', 'shared@example.com', 'Teacher', 'Degree'],
    ]);
    (new Xlsx($spreadsheet))->save($sourcePath);

    try {
        $seeder = (new TeacherDataSeeder($sourcePath))->setContainer($this->app);
        $seeder->run();
        $seeder->run();

        $principal = User::query()->where('email', 'shared@example.com')->firstOrFail();
        $teacher = User::query()->where('email', 'shared+17536@example.com')->firstOrFail();
        $firstProfile = Teacher::query()->where('ttis_id', '17535')->firstOrFail();
        $secondProfile = Teacher::query()->where('ttis_id', '17536')->firstOrFail();

        expect(Hash::check('12345678', $principal->password))->toBeTrue()
            ->and($principal->mobile_no)->toBe('01712345678')
            ->and($principal->hasRole('principal'))->toBeTrue()
            ->and($teacher->hasRole('teacher'))->toBeTrue()
            ->and($firstProfile->college->college_code)->toBe('101')
            ->and($firstProfile->college->college_type)->toBe('government')
            ->and($firstProfile->college->principal_name)->toBe('FIRST TEACHER')
            ->and($firstProfile->designation->name)->toBe('Professor')
            ->and($firstProfile->subject->name)->toBe('Management')
            ->and($firstProfile->teacherLevel->name)->toBe('Honours')
            ->and($firstProfile->birth_date->toDateString())->toBe('1961-12-18')
            ->and($secondProfile->subject->name)->toBe('Business Administration (BBA)')
            ->and($secondProfile->teacherLevel->name)->toBe('Degree')
            ->and(Teacher::query()->count())->toBe(2)
            ->and(User::query()->count())->toBe(2);
    } finally {
        if (is_file($sourcePath)) {
            unlink($sourcePath);
        }
    }
});

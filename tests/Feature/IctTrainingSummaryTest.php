<?php

use App\Exports\SummaryExport;
use App\Livewire\IctTrainingSummary;
use App\Models\College;
use App\Models\Designation;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherLevel;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

function createSummaryTeacher(string $name, string $collegeCode, bool $withTraining = false): Teacher
{
    $college = College::query()->firstOrCreate(['college_code' => $collegeCode], ['name' => "College {$collegeCode}"]);
    $teacher = Teacher::query()->create(['name' => $name, 'college_id' => $college->id]);

    if ($withTraining) {
        $institute = TrainingInstitute::query()->firstOrCreate(['name' => 'NAEM']);
        $trainingType = TrainingType::query()->firstOrCreate(
            ['training_institute_id' => $institute->id, 'name' => 'Digital Content Creation'],
            ['duration_value' => 5, 'duration_unit' => 'days'],
        );
        $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => 2025]);
    }

    return $teacher;
}

test('training summary separates teachers using normalized training relationships', function () {
    createSummaryTeacher('ICT Teacher', '1001', true);
    createSummaryTeacher('Teacher Without Training', '1001');

    Livewire::test(IctTrainingSummary::class)
        ->assertSee('ICT Teacher')
        ->assertSee('Digital Content Creation')
        ->assertDontSee('Teacher Without Training')
        ->call('showTab', 'without_ict')
        ->assertSee('Teacher Without Training')
        ->assertDontSee('ICT Teacher');
});

test('teachers without training show normalized professional details', function () {
    $teacher = createSummaryTeacher('Teacher With Professional Details', '1001');
    $subject = Subject::query()->create(['name' => 'Accounting']);
    $designation = Designation::query()->create(['name' => 'Assistant Professor']);
    $teacherLevel = TeacherLevel::query()->create(['name' => 'Degree']);
    $employment = Employment::query()->create(['name' => 'Permanent']);
    $teacher->update([
        'subject_id' => $subject->id,
        'designation_id' => $designation->id,
        'teacher_level_id' => $teacherLevel->id,
        'employment_id' => $employment->id,
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->call('showTab', 'without_ict')
        ->assertSee('Accounting')
        ->assertSee('Assistant Professor')
        ->assertSee('Degree')
        ->assertSee('Permanent');
});

test('training summary paginates records and only loads the active tab', function () {
    foreach (range(1, 51) as $index) {
        createSummaryTeacher("Trained Teacher {$index}", '1001', true);
    }
    createSummaryTeacher('Teacher Without Training', '1002');

    Livewire::test(IctTrainingSummary::class)
        ->assertViewHas('teachers', fn ($teachers): bool => $teachers->count() === 50 && $teachers->total() === 51)
        ->assertDontSee('Teacher Without Training')
        ->call('showTab', 'without_ict')
        ->assertViewHas('teachers', fn ($teachers): bool => $teachers->count() === 1 && $teachers->total() === 1)
        ->assertSee('Teacher Without Training');
});

test('each training tab can be exported to its own spreadsheet', function (string $tab, string $filename, bool $withTraining) {
    Excel::fake();
    createSummaryTeacher('Exported Teacher', '1001', $withTraining);

    Livewire::test(IctTrainingSummary::class)->call('export', $tab);

    Excel::assertDownloaded($filename);
})->with([
    ['with_ict', 'teachers-with-ict-training.xlsx', true],
    ['without_ict', 'teachers-without-ict-training.xlsx', false],
]);

test('teachers without training export includes normalized professional details', function () {
    Excel::fake();
    $teacher = createSummaryTeacher('Exported Teacher Details', '1001');
    $teacher->college->update(['name' => 'Export College']);
    $teacher->update([
        'subject_id' => Subject::query()->create(['name' => 'Accounting'])->id,
        'designation_id' => Designation::query()->create(['name' => 'Assistant Professor'])->id,
        'teacher_level_id' => TeacherLevel::query()->create(['name' => 'Degree'])->id,
        'employment_id' => Employment::query()->create(['name' => 'Permanent'])->id,
    ]);

    Livewire::test(IctTrainingSummary::class)->call('export', 'without_ict');

    Excel::assertDownloaded('teachers-without-ict-training.xlsx', function (SummaryExport $export): bool {
        expect($export->array())->toBe([
            [1, '1001', 'Export College', 'Exported Teacher Details', 'Accounting', 'Assistant Professor', 'Degree', 'Permanent', 'ট্রেনিং নেই'],
        ]);

        return true;
    });
});

test('teacher serial numbers restart for every college', function () {
    createSummaryTeacher('Teacher 1001', '1001');
    createSummaryTeacher('Teacher 1002', '1002');

    $component = Livewire::test(IctTrainingSummary::class)->call('showTab', 'without_ict');

    expect($component->html())->toMatch('/College 1001.*?>1<.*?College 1002.*?>1</s');
});

<?php

use App\Livewire\IctTrainingSummary;
use App\Models\Teacher;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

test('training summary shows non-empty ICT training names without marker filtering', function (string $trainingName) {
    Teacher::query()->create([
        'name' => 'Teacher With Training Data',
        'college_code' => '1001',
        'ict_training_name' => $trainingName,
        'other_training_name' => 'Other Training Data',
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->assertViewHas(
            'teachersByCollege',
            fn ($teachers): bool => $teachers->flatten(1)->pluck('name')->contains('Teacher With Training Data'),
        );
})->with(['N/A', 'No', 'NO', '-', '---', 'Nill', 'NA', '0', 'No training', ' no training ']);

test('other training name does not filter a teacher with an ICT training name', function () {
    Teacher::query()->create([
        'name' => 'ICT Teacher',
        'college_code' => '1001',
        'ict_training_name' => 'Digital Content Creation',
        'other_training_name' => 'N/A',
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->assertSee('ICT Teacher')
        ->assertSee('Digital Content Creation')
        ->assertSee('N/A');
});

test('training summary lists teachers with an empty ICT training name as without ICT training', function (?string $trainingName) {
    Teacher::query()->create([
        'name' => 'Teacher Without ICT Training',
        'college_code' => '1001',
        'ict_training_name' => $trainingName,
        'other_training_name' => 'Office Management',
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->call('showTab', 'without_ict')
        ->assertViewHas(
            'teachersByCollege',
            fn ($teachers): bool => $teachers->flatten(1)->pluck('name')->contains('Teacher Without ICT Training'),
        );
})->with([null, '']);

test('training summary paginates records and only loads the active tab', function () {
    foreach (range(1, 51) as $index) {
        Teacher::query()->create([
            'name' => "Trained Teacher {$index}",
            'college_code' => '1001',
            'ict_training_name' => 'Digital Content Creation',
        ]);
    }

    Teacher::query()->create([
        'name' => 'Teacher Without Training',
        'college_code' => '1002',
        'ict_training_name' => null,
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->assertViewHas('teachers', fn ($teachers): bool => $teachers->count() === 50 && $teachers->total() === 51)
        ->assertDontSee('Teacher Without Training')
        ->call('showTab', 'without_ict')
        ->assertSet('activeTab', 'without_ict')
        ->assertViewHas('teachers', fn ($teachers): bool => $teachers->count() === 1 && $teachers->total() === 1)
        ->assertSee('Teacher Without Training');
});

test('each ICT training tab can be exported to its own spreadsheet', function (string $tab, string $filename) {
    Excel::fake();

    Teacher::query()->create([
        'name' => 'Exported Teacher',
        'college_code' => '1001',
        'college_name' => 'Export College',
        'ict_training_name' => $tab === 'with_ict' ? 'Digital Content Creation' : null,
    ]);

    Livewire::test(IctTrainingSummary::class)->call('export', $tab);

    Excel::assertDownloaded($filename);
})->with([
    ['with_ict', 'teachers-with-ict-training.xlsx'],
    ['without_ict', 'teachers-without-ict-training.xlsx'],
]);

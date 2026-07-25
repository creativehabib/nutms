<?php

use App\Livewire\IctTrainingSummary;
use App\Models\Teacher;
use Livewire\Livewire;

test('training summary shows non-empty ICT training names without marker filtering', function (string $trainingName) {
    Teacher::query()->create([
        'name' => 'Teacher With Training Data',
        'college_code' => '1001',
        'ict_training_name' => $trainingName,
        'other_training_name' => 'Other Training Data',
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->assertViewHas(
            'teachersWithIct',
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
        ->assertViewHas(
            'teachersWithoutIct',
            fn ($teachers): bool => $teachers->flatten(1)->pluck('name')->contains('Teacher Without ICT Training'),
        );
})->with([null, '']);

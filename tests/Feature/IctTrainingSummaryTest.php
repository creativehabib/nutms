<?php

use App\Livewire\IctTrainingSummary;
use App\Models\Teacher;
use Livewire\Livewire;

test('training summary hides non-training values from the trained teachers list', function (string $nonTrainingValue) {
    Teacher::query()->create([
        'name' => 'Teacher Without Training',
        'college_code' => '1001',
        'ict_training_name' => $nonTrainingValue,
        'other_training_name' => $nonTrainingValue,
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->assertViewHas('teachersWithIct', fn ($teachers): bool => $teachers->flatten(1)->isEmpty())
        ->assertViewHas('teachersWithoutIct', fn ($teachers): bool => $teachers->flatten(1)->pluck('name')->contains('Teacher Without Training'));
})->with(['N/A', 'No', 'NO', '-', '---', 'Nill', 'NA', '0', 'No training', ' no training ']);

test('training summary includes teachers with a meaningful training name in either training field', function () {
    Teacher::query()->create([
        'name' => 'ICT Teacher',
        'college_code' => '1001',
        'ict_training_name' => 'Digital Content Creation',
        'other_training_name' => 'N/A',
    ]);

    Teacher::query()->create([
        'name' => 'Other Training Teacher',
        'college_code' => '1001',
        'ict_training_name' => 'No',
        'other_training_name' => 'Office Management',
    ]);

    Livewire::test(IctTrainingSummary::class)
        ->assertSee('ICT Teacher')
        ->assertSee('Digital Content Creation')
        ->assertSee('Other Training Teacher')
        ->assertSee('Office Management');
});

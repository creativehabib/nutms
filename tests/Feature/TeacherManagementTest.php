<?php

use App\Livewire\TeacherManagement;
use App\Models\Teacher;
use Livewire\Livewire;

it('renders a responsive edit form with a blurred backdrop', function () {
    Livewire::test(TeacherManagement::class)
        ->assertSeeHtml('backdrop-blur-sm')
        ->assertSeeHtml('sm:max-h-[calc(100vh-3rem)]')
        ->assertSeeHtml('px-3 py-2.5');
});

it('allows every teacher data field to be updated', function () {
    $teacher = Teacher::query()->create([
        'college_code' => '100',
        'college_name' => 'Old College',
        'tmis_id' => 'TMIS-OLD',
        'name' => 'Old Name',
    ]);

    $updatedData = [
        'college_code' => '200',
        'college_name' => 'Updated College',
        'tmis_id' => 'TMIS-NEW',
        'ttis_id' => 'TTIS-NEW',
        'name' => 'Updated Teacher',
        'designation' => 'Assistant Professor',
        'subject' => 'Physics',
        'teacher_level' => 'College',
        'employment_type' => 'Permanent',
        'has_training' => 'Yes',
        'ict_training_name' => 'Digital Content',
        'ict_training_duration' => '10 days',
        'other_training_name' => 'Management',
        'other_training_duration' => '5 days',
        'training_institute' => 'NAEM',
        'training_year' => '2026',
        'has_computer_lab' => 'Yes',
        'computer_count' => 25,
        'mobile_number' => '01700000000',
        'email' => 'teacher@example.com',
    ];

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('editForm', $updatedData)
        ->call('updateTeacher')
        ->assertHasNoErrors()
        ->assertDispatched('close-edit-modal');

    expect($teacher->refresh()->only(array_keys($updatedData)))->toBe($updatedData);
});

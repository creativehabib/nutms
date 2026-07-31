<?php

use App\Livewire\ReferenceDataManagement;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Livewire;

it('protects all reference data pages with authentication', function (string $type) {
    $this->get(route('reference-data.manage', $type))->assertRedirect(route('login'));
})->with(['subjects', 'designations', 'teacher-levels', 'employments']);

it('creates and updates subject reference data', function () {
    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->set('name', 'Physics')->call('save')->assertHasNoErrors();

    $subject = Subject::query()->where('name', 'Physics')->firstOrFail();

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->call('edit', $subject->id)->set('name', 'Applied Physics')->set('isActive', false)->call('save')->assertHasNoErrors();

    expect($subject->refresh()->name)->toBe('Applied Physics')->and($subject->is_active)->toBeFalse();
});

it('keeps legacy teacher values and relationships synchronized when reference data changes', function () {
    $subject = Subject::query()->create(['name' => 'Physics']);
    $teacher = Teacher::query()->create(['name' => 'Teacher', 'subject' => 'Physics', 'subject_id' => $subject->id]);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->call('edit', $subject->id)->set('name', 'Applied Physics')->call('save');

    expect($teacher->refresh()->subject_id)->toBe($subject->id)->and($teacher->subject)->toBe('Applied Physics');
});

it('does not delete reference data used by a teacher', function () {
    $subject = Subject::query()->create(['name' => 'Physics']);
    Teacher::query()->create(['name' => 'Teacher', 'subject_id' => $subject->id]);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])->call('delete', $subject->id);

    expect($subject->fresh())->not->toBeNull();
});

it('allows authenticated users to open a reference data page', function () {
    $this->actingAs(User::factory()->create())->get(route('reference-data.manage', 'subjects'))->assertSuccessful();
});

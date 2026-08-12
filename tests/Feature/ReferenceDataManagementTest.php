<?php

use App\Livewire\ReferenceDataManagement;
use App\Models\College;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\ProgramLevelSeeder;
use Livewire\Livewire;

it('protects all reference data pages with authentication', function (string $type) {
    $this->get(route('reference-data.manage', $type))->assertRedirect(route('login'));
})->with(['subjects', 'courses', 'designations', 'teacher-levels', 'employments']);

it('allows admins to create update and delete courses', function () {
    $this->seed(ProgramLevelSeeder::class);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'courses'])
        ->call('openCreateModal')
        ->set('name', 'Bachelor of Arts')
        ->set('level', 'degree')
        ->call('save')
        ->assertHasNoErrors();

    $course = Course::query()->where('name', 'Bachelor of Arts')->firstOrFail();

    Livewire::test(ReferenceDataManagement::class, ['type' => 'courses'])
        ->call('edit', $course->id)
        ->set('name', 'Bachelor of Arts Pass')
        ->set('isActive', false)
        ->call('save')
        ->assertHasNoErrors();

    expect($course->refresh()->name)->toBe('Bachelor of Arts Pass')->and($course->is_active)->toBeFalse();

    Livewire::test(ReferenceDataManagement::class, ['type' => 'courses'])
        ->call('confirmDelete', $course->id)
        ->call('deleteConfirmed');

    expect($course->fresh())->toBeNull();
});

it('keeps affiliated course names synchronized and prevents deleting courses in use', function () {
    $this->seed(ProgramLevelSeeder::class);
    $course = Course::query()->create(['name' => 'BA', 'level' => 'degree']);
    $college = College::query()->create(['name' => 'Course College']);
    $program = $college->programs()->create(['level' => 'degree', 'name' => 'BA', 'items' => ['BA']]);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'courses'])
        ->call('edit', $course->id)
        ->set('name', 'BA Pass')
        ->call('save')
        ->call('confirmDelete', $course->id)
        ->assertSet('showDeleteModal', false);

    expect($program->refresh()->items)->toBe(['BA Pass'])
        ->and($program->name)->toBe('BA Pass')
        ->and($course->fresh())->not->toBeNull();

    Livewire::test(ReferenceDataManagement::class, ['type' => 'courses'])
        ->call('edit', $course->id)
        ->set('level', 'professional')
        ->call('save')
        ->assertHasErrors(['level']);
});

it('creates and updates subject reference data', function () {
    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->call('openCreateModal')
        ->assertSet('showModal', true)
        ->set('name', 'Physics')->call('save')->assertHasNoErrors()
        ->assertSet('showModal', false);

    $subject = Subject::query()->where('name', 'Physics')->firstOrFail();

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->call('edit', $subject->id)
        ->assertSet('showModal', true)
        ->set('name', 'Applied Physics')->set('isActive', false)->call('save')->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect($subject->refresh()->name)->toBe('Applied Physics')->and($subject->is_active)->toBeFalse();
});

it('keeps teacher relationships synchronized when reference data changes', function () {
    $subject = Subject::query()->create(['name' => 'Physics']);
    $teacher = Teacher::query()->create(['name' => 'Teacher', 'subject_id' => $subject->id]);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->call('edit', $subject->id)->set('name', 'Applied Physics')->call('save');

    expect($teacher->refresh()->subject_id)->toBe($subject->id)->and($teacher->subject()->value('name'))->toBe('Applied Physics');
});

it('does not delete reference data used by a teacher', function () {
    $subject = Subject::query()->create(['name' => 'Physics']);
    Teacher::query()->create(['name' => 'Teacher', 'subject_id' => $subject->id]);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])->call('confirmDelete', $subject->id);

    expect($subject->fresh())->not->toBeNull();
});

it('allows authenticated users to open a reference data page', function () {
    $this->actingAs(User::factory()->create())->get(route('reference-data.manage', 'subjects'))->assertSuccessful();
});

it('uses the Flux modal before deleting reference data', function () {
    $subject = Subject::query()->create(['name' => 'Temporary Subject']);

    Livewire::test(ReferenceDataManagement::class, ['type' => 'subjects'])
        ->call('confirmDelete', $subject->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deletingName', 'Temporary Subject')
        ->call('deleteConfirmed')
        ->assertSet('showDeleteModal', false);

    expect($subject->fresh())->toBeNull()
        ->and(file_get_contents(resource_path('views/livewire/reference-data-management.blade.php')))->not->toContain('wire:confirm');
});

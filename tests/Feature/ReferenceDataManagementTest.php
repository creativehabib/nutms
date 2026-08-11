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

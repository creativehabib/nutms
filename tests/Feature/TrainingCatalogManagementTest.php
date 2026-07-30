<?php

use App\Livewire\IctTrainingSummary;
use App\Livewire\TeacherManagement;
use App\Livewire\TrainingCatalogManagement;
use App\Models\Teacher;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\User;
use Livewire\Livewire;

it('requires authentication to manage the training catalog', function () {
    $this->get(route('training-catalog.manage'))->assertRedirect(route('login'));
});

it('allows authenticated users to open the training catalog', function () {
    $this->actingAs(User::factory()->create())->get(route('training-catalog.manage'))->assertSuccessful();
});

it('creates an institute and an institute-specific training type with duration', function () {
    Livewire::test(TrainingCatalogManagement::class)
        ->set('instituteName', 'NAEM')
        ->call('saveInstitute')
        ->assertHasNoErrors();

    $institute = TrainingInstitute::query()->where('name', 'NAEM')->firstOrFail();

    Livewire::test(TrainingCatalogManagement::class)
        ->set('trainingInstituteId', (string) $institute->id)
        ->set('trainingTypeName', 'Digital Content Development')
        ->set('durationValue', '10')
        ->set('durationUnit', 'days')
        ->call('saveTrainingType')
        ->assertHasNoErrors();

    $trainingType = TrainingType::query()->firstOrFail();
    expect($trainingType->training_institute_id)->toBe($institute->id)
        ->and($trainingType->duration_value)->toBe(10)
        ->and($trainingType->duration_unit)->toBe('days');
});

it('stores the completion year on each teachers training record', function () {
    $institute = TrainingInstitute::query()->create(['name' => 'NAEM']);
    $trainingType = TrainingType::query()->create([
        'training_institute_id' => $institute->id,
        'name' => 'Digital Content Development',
        'duration_value' => 10,
        'duration_unit' => 'days',
    ]);
    $teacher = Teacher::query()->create(['name' => 'Teacher']);

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('trainingEntries', [[
            'kind' => 'catalog',
            'training_institute_id' => (string) $institute->id,
            'training_type_id' => (string) $trainingType->id,
            'training_year' => '2025',
        ]])
        ->call('updateTeacher')
        ->assertHasNoErrors();

    expect($teacher->refresh()->trainingTypes)->toHaveCount(1)
        ->and($teacher->trainingTypes->first()->pivot->training_year)->toBe(2025);
});

it('rejects a training type that does not belong to the selected institute', function () {
    $selectedInstitute = TrainingInstitute::query()->create(['name' => 'Selected Institute']);
    $differentInstitute = TrainingInstitute::query()->create(['name' => 'Different Institute']);
    $trainingType = TrainingType::query()->create([
        'training_institute_id' => $differentInstitute->id,
        'name' => 'Mismatched Training',
        'duration_value' => 5,
        'duration_unit' => 'days',
    ]);
    $teacher = Teacher::query()->create(['name' => 'Teacher']);

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('trainingEntries', [[
            'kind' => 'catalog',
            'training_institute_id' => (string) $selectedInstitute->id,
            'training_type_id' => (string) $trainingType->id,
            'training_year' => '2025',
        ]])
        ->call('updateTeacher')
        ->assertHasErrors(['trainingEntries.0.training_type_id']);

    expect($teacher->refresh()->trainingTypes)->toBeEmpty();
});

it('protects training types that are used by teachers from deletion', function () {
    $institute = TrainingInstitute::query()->create(['name' => 'NAEM']);
    $trainingType = TrainingType::query()->create([
        'training_institute_id' => $institute->id,
        'name' => 'Protected Training',
        'duration_value' => 3,
        'duration_unit' => 'days',
    ]);
    $teacher = Teacher::query()->create(['name' => 'Teacher']);
    $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => 2024]);

    Livewire::test(TrainingCatalogManagement::class)->call('deleteTrainingType', $trainingType->id);

    expect($trainingType->fresh())->not->toBeNull();
});

it('uses the existing teacher training pivot table from both relationship directions', function () {
    $institute = TrainingInstitute::query()->create(['name' => 'Relationship Institute']);
    $trainingType = TrainingType::query()->create([
        'training_institute_id' => $institute->id,
        'name' => 'Relationship Training',
        'duration_value' => 2,
        'duration_unit' => 'days',
    ]);
    $teacher = Teacher::query()->create(['name' => 'Relationship Teacher']);
    $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => 2025]);

    expect($teacher->trainingTypes()->getTable())->toBe('teacher_training')
        ->and($trainingType->teachers()->getTable())->toBe('teacher_training')
        ->and($trainingType->teachers()->count())->toBe(1)
        ->and($teacher->trainingTypes()->count())->toBe(1);
});

it('shows normalized training, completion year, and duration in the training summary', function () {
    $institute = TrainingInstitute::query()->create(['name' => 'NAEM']);
    $trainingType = TrainingType::query()->create(['training_institute_id' => $institute->id, 'name' => 'Digital Content', 'duration_value' => 10, 'duration_unit' => 'days']);
    $teacher = Teacher::query()->create(['name' => 'Trained Teacher', 'college_code' => '1001']);
    $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => 2025]);

    Livewire::test(IctTrainingSummary::class)
        ->assertSee('Trained Teacher')
        ->assertSee('Digital Content (2025, 10 দিন)')
        ->assertSee('NAEM');
});

it('stores an uncatalogued other training with its own institute duration and year', function () {
    $teacher = Teacher::query()->create(['name' => 'Teacher With Other Training']);

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('trainingEntries', [[
            'kind' => 'other',
            'training_institute_id' => '',
            'institute_name' => 'International Training Centre',
            'training_type_id' => '',
            'name' => 'Inclusive Education Workshop',
            'duration_value' => '3',
            'duration_unit' => 'days',
            'training_year' => '2026',
        ]])
        ->call('updateTeacher')
        ->assertHasNoErrors();

    $otherTraining = $teacher->refresh()->otherTrainings()->firstOrFail();
    expect($otherTraining->name)->toBe('Inclusive Education Workshop')
        ->and($otherTraining->institute_name)->toBe('International Training Centre')
        ->and($otherTraining->duration_value)->toBe(3)
        ->and($otherTraining->training_year)->toBe(2026);

    Livewire::test(IctTrainingSummary::class)
        ->assertSee('Teacher With Other Training')
        ->assertSee('Inclusive Education Workshop (অন্যান্য, 2026, 3 দিন)')
        ->assertSee('International Training Centre');
});

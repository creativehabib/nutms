<?php

use App\Livewire\TeacherManagement;
use App\Models\Teacher;
use Livewire\Livewire;

it('renders a responsive edit form with a blurred backdrop', function () {
    Livewire::test(TeacherManagement::class)
        ->assertSeeHtml('backdrop-blur-sm')
        ->assertSeeHtml('sm:max-h-[calc(100vh-3rem)]')
        ->assertSeeHtml('px-3 py-2.5')
        ->assertSee('শিক্ষক খুঁজুন')
        ->assertSee('এই পৃষ্ঠার সব শিক্ষক নির্বাচন করুন')
        ->assertSeeHtml('lg:grid-cols-[minmax(16rem,1.25fr)_repeat(3,minmax(10rem,0.75fr))_auto]');
});

it('shows the distinct college count beside the teacher count', function () {
    Teacher::query()->create(['college_code' => '100', 'name' => 'First Teacher']);
    Teacher::query()->create(['college_code' => '100', 'name' => 'Second Teacher']);
    Teacher::query()->create(['college_code' => '200', 'name' => 'Third Teacher']);

    Livewire::test(TeacherManagement::class)
        ->assertSee('মোট 3 জন শিক্ষক')
        ->assertSee('মোট 2টি কলেজ');
});

it('keeps every row checkbox checked when selecting the current page', function () {
    Teacher::query()->create(['name' => 'First Selected Teacher']);
    Teacher::query()->create(['name' => 'Second Selected Teacher']);
    $expectedTeacherIds = Teacher::query()->latest()->pluck('id')->map(fn (int $id): string => (string) $id)->all();

    $component = Livewire::test(TeacherManagement::class)
        ->call('toggleSelectAllOnPage')
        ->assertSet('selectAllOnPage', true)
        ->assertSet('selectedTeacherIds', $expectedTeacherIds);

    foreach ($expectedTeacherIds as $teacherId) {
        expect($component->html())->toMatch('/value="'.preg_quote($teacherId, '/').'"[^>]*checked/');
    }
});

it('uses the same explicit multi-select behavior in active and trash tables', function () {
    $activeTeacher = Teacher::query()->create(['name' => 'Active Teacher']);
    $trashedTeacher = Teacher::query()->create(['name' => 'Trashed Teacher']);
    $trashedTeacher->delete();

    Livewire::test(TeacherManagement::class)
        ->call('toggleTeacherSelection', $activeTeacher->id)
        ->assertSet('selectedTeacherIds', [(string) $activeTeacher->id])
        ->call('toggleTeacherSelection', $activeTeacher->id)
        ->assertSet('selectedTeacherIds', [])
        ->call('toggleTrashed')
        ->call('toggleTeacherSelection', $trashedTeacher->id)
        ->assertSet('selectedTeacherIds', [(string) $trashedTeacher->id]);
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

it('shows user friendly validation errors while editing teacher data', function () {
    $teacher = Teacher::query()->create([
        'tmis_id' => 'TMIS-ONE',
        'name' => 'Existing Teacher',
    ]);

    Teacher::query()->create([
        'tmis_id' => 'TMIS-TWO',
        'name' => 'Another Teacher',
    ]);

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('editForm.name', '')
        ->set('editForm.tmis_id', 'TMIS-TWO')
        ->set('editForm.email', 'invalid-email')
        ->call('updateTeacher')
        ->assertHasErrors([
            'editForm.name' => 'required',
            'editForm.tmis_id' => 'unique',
            'editForm.email' => 'email',
        ])
        ->assertSee('তথ্য আপডেট করা যায়নি')
        ->assertSee('শিক্ষকের নাম অবশ্যই দিতে হবে।')
        ->assertSee('এই TMIS ID ইতোমধ্যে অন্য একজন শিক্ষকের জন্য ব্যবহার করা হয়েছে।')
        ->assertSee('সঠিক ইমেইল ঠিকানা লিখুন।')
        ->assertNotDispatched('close-edit-modal');
});

it('requires Flux confirmation before deleting a teacher', function () {
    $teacher = Teacher::query()->create([
        'name' => 'Teacher To Delete',
    ]);

    Livewire::test(TeacherManagement::class)
        ->call('confirmTeacherDeletion', $teacher->id)
        ->assertSet('deletingTeacherIds', [$teacher->id])
        ->assertSet('deletingTeacherName', 'Teacher To Delete')
        ->assertSee('শিক্ষকের তথ্য মুছে ফেলবেন?')
        ->assertSee('Teacher To Delete')
        ->call('deleteTeacher');

    expect(Teacher::query()->find($teacher->id))->toBeNull()
        ->and(Teacher::withTrashed()->find($teacher->id)?->deleted_at)->not->toBeNull();
});

it('selects and deletes multiple teachers after confirmation', function () {
    $teachers = collect([
        Teacher::query()->create(['name' => 'First Teacher']),
        Teacher::query()->create(['name' => 'Second Teacher']),
        Teacher::query()->create(['name' => 'Teacher To Keep']),
    ]);

    Livewire::test(TeacherManagement::class)
        ->set('selectedTeacherIds', $teachers->take(2)->pluck('id')->map(fn (int $id): string => (string) $id)->all())
        ->call('confirmBulkTeacherDeletion')
        ->assertSet('deletingTeacherIds', $teachers->take(2)->pluck('id')->all())
        ->assertSet('deletingTeacherName', 'নির্বাচিত 2 জন শিক্ষক')
        ->assertSee('নির্বাচিত 2 জন শিক্ষক')
        ->call('deleteTeacher')
        ->assertSet('selectedTeacherIds', []);

    expect(Teacher::query()->find($teachers[0]->id))->toBeNull()
        ->and(Teacher::query()->find($teachers[1]->id))->toBeNull()
        ->and(Teacher::query()->find($teachers[2]->id))->not->toBeNull();
});

it('restores a soft deleted teacher from the trash', function () {
    $teacher = Teacher::query()->create(['name' => 'Restorable Teacher']);
    $teacher->delete();

    Livewire::test(TeacherManagement::class)
        ->call('toggleTrashed')
        ->assertSet('showTrashed', true)
        ->assertSee('Restorable Teacher')
        ->call('restoreTeacher', $teacher->id);

    expect($teacher->fresh())->not->toBeNull()
        ->and($teacher->refresh()->deleted_at)->toBeNull();
});

it('restores multiple selected teachers from the trash', function () {
    $teachers = collect([
        Teacher::query()->create(['name' => 'First Restorable Teacher']),
        Teacher::query()->create(['name' => 'Second Restorable Teacher']),
    ]);

    $teachers->each->delete();

    Livewire::test(TeacherManagement::class)
        ->call('toggleTrashed')
        ->set('selectedTeacherIds', $teachers->pluck('id')->map(fn (int $id): string => (string) $id)->all())
        ->call('restoreSelectedTeachers')
        ->assertSet('selectedTeacherIds', []);

    expect(Teacher::query()->count())->toBe(2);
});

<?php

use App\Enums\ApprovalStatus;
use App\Livewire\TeacherManagement;
use App\Livewire\TeacherProfileForm;
use App\Models\College;
use App\Models\Designation;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherLevel;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('does not keep obsolete legacy training columns on teachers', function () {
    expect(Schema::hasTable('teachers'))->toBeFalse()
        ->and(Schema::hasTable('teacher_profiles'))->toBeTrue()
        ->and(Schema::hasColumn('users', 'teacher_id'))->toBeFalse()
        ->and(Schema::hasColumn('teacher_profiles', 'birth_date'))->toBeTrue()
        ->and(Schema::hasTable('system_settings'))->toBeTrue()
        ->and(Schema::hasColumn('teacher_profiles', 'has_training'))->toBeFalse()
        ->and(Schema::hasColumn('teacher_profiles', 'ict_training_duration'))->toBeFalse()
        ->and(Schema::hasColumn('teacher_profiles', 'other_training_duration'))->toBeFalse()
        ->and(Schema::hasColumn('teacher_profiles', 'training_year'))->toBeFalse();
});

it('creates the final teacher profile schema without a cleanup migration', function () {
    expect(Schema::hasTable('teacher_profiles'))->toBeTrue()
        ->and(Schema::getColumnListing('teacher_profiles'))->toContain('user_id', 'college_id', 'approval_status')
        ->not->toContain(
            'college_code',
            'college_name',
            'designation',
            'subject',
            'teacher_level',
            'employment_type',
            'ict_training_name',
            'other_training_name',
            'training_institute',
            'has_computer_lab',
            'computer_count',
            'has_training',
            'ict_training_duration',
            'other_training_duration',
            'training_year',
        );
});

it('does not require the legacy user teacher foreign key', function () {
    expect(Schema::hasColumn('users', 'teacher_id'))->toBeFalse()
        ->and(Schema::hasTable('teachers'))->toBeFalse()
        ->and(Schema::hasTable('teacher_profiles'))->toBeTrue();
});

it('uses conventional subject and designation relationships', function () {
    $subject = Subject::query()->create(['name' => 'Physics']);
    $designation = Designation::query()->create(['name' => 'Lecturer']);
    $teacherLevel = TeacherLevel::query()->create(['name' => 'College']);
    $employment = Employment::query()->create(['name' => 'Permanent']);
    $teacher = Teacher::query()->create([
        'name' => 'Relationship Teacher',
        'subject_id' => $subject->id,
        'designation_id' => $designation->id,
        'teacher_level_id' => $teacherLevel->id,
        'employment_id' => $employment->id,
    ]);

    expect($teacher->subject->is($subject))->toBeTrue()
        ->and($teacher->designation->is($designation))->toBeTrue()
        ->and($teacher->subject()->getModel())->toBeInstanceOf(Subject::class)
        ->and($teacher->designation()->getModel())->toBeInstanceOf(Designation::class)
        ->and($teacher->teacherLevel->is($teacherLevel))->toBeTrue()
        ->and($teacher->employment->is($employment))->toBeTrue()
        ->and($teacher->teacher_level_id)->toBe($teacherLevel->id)
        ->and($teacher->employment_id)->toBe($employment->id);
});

it('renders a responsive edit form with a blurred backdrop', function () {
    Livewire::test(TeacherManagement::class)
        ->assertSeeHtml('backdrop-blur-sm')
        ->assertSeeHtml('sm:max-h-[calc(100vh-3rem)]')
        ->assertSeeHtml('px-3 py-2.5')
        ->assertSee('শিক্ষক খুঁজুন')
        ->assertSee('এই পৃষ্ঠার সব শিক্ষক নির্বাচন করুন')
        ->assertSeeHtml('lg:grid-cols-[minmax(16rem,1.25fr)_repeat(2,minmax(10rem,0.75fr))_auto]');
});

it('shows the distinct college count beside the teacher count', function () {
    $firstCollege = College::query()->create(['college_code' => '100', 'name' => 'First College']);
    $secondCollege = College::query()->create(['college_code' => '200', 'name' => 'Second College']);
    Teacher::query()->create(['college_id' => $firstCollege->id, 'name' => 'First Teacher']);
    Teacher::query()->create(['college_id' => $firstCollege->id, 'name' => 'Second Teacher']);
    Teacher::query()->create(['college_id' => $secondCollege->id, 'name' => 'Third Teacher']);

    Livewire::test(TeacherManagement::class)
        ->assertSee('মোট 3 জন শিক্ষক')
        ->assertSee('মোট 2টি কলেজ');
});

it('lets staff create a teacher login account without completing the full profile', function () {
    $college = College::query()->create([
        'name' => 'Account Bootstrap College',
        'approval_status' => ApprovalStatus::Approved,
        'is_active' => true,
    ]);

    Livewire::test(TeacherManagement::class)
        ->assertSee('নতুন শিক্ষক');

    Livewire::test(TeacherProfileForm::class)
        ->set('collegeId', (string) $college->id)
        ->set('name', 'New Account Teacher')
        ->set('accountEmail', 'new.teacher@example.com')
        ->set('mobileNumber', '01722222222')
        ->set('accountPassword', 'password')
        ->set('accountPassword_confirmation', 'password')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'new.teacher@example.com')->first();
    $teacher = Teacher::query()->where('user_id', $user?->id)->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('teacher'))->toBeTrue()
        ->and($user->college_id)->toBe($college->id)
        ->and($user->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher)->not->toBeNull()
        ->and($teacher->name)->toBe('New Account Teacher')
        ->and($user->mobile_no)->toBe('01722222222')
        ->and($teacher->college_id)->toBe($college->id)
        ->and($teacher->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher->present_address)->toBeNull();
});

it('lets an admin toggle a teacher approval status from the table', function () {
    $teacher = Teacher::query()->create([
        'name' => 'Approval Toggle Teacher',
        'approval_status' => ApprovalStatus::Pending,
    ]);

    Livewire::test(TeacherManagement::class)
        ->assertSee('Approval Toggle Teacher')
        ->assertSeeHtml('wire:click="toggleTeacherApproval('.$teacher->id.')"')
        ->call('toggleTeacherApproval', $teacher->id)
        ->assertHasNoErrors()
        ->assertSee('অনুমোদিত');

    expect($teacher->refresh()->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher->approved_by)->toBe(auth()->id())
        ->and($teacher->approved_at)->not->toBeNull();

    Livewire::test(TeacherManagement::class)
        ->call('toggleTeacherApproval', $teacher->id)
        ->assertHasNoErrors()
        ->assertSee('পেন্ডিং');

    expect($teacher->refresh()->approval_status)->toBe(ApprovalStatus::Pending)
        ->and($teacher->approved_by)->toBeNull()
        ->and($teacher->approved_at)->toBeNull();
});

it('lets an admin reject a pending teacher profile from the table actions', function () {
    $teacher = Teacher::query()->create([
        'name' => 'Admin Rejection Teacher',
        'approval_status' => ApprovalStatus::Pending,
    ]);

    Livewire::test(TeacherManagement::class)
        ->assertSee('Admin Rejection Teacher')
        ->assertSeeHtml('wire:click="rejectTeacher('.$teacher->id.')"')
        ->assertSee('প্রত্যাখ্যান')
        ->call('rejectTeacher', $teacher->id)
        ->assertHasNoErrors()
        ->assertSee('প্রত্যাখ্যাত');

    expect($teacher->refresh()->approval_status)->toBe(ApprovalStatus::Rejected)
        ->and($teacher->approved_by)->toBe(auth()->id())
        ->and($teacher->approved_at)->not->toBeNull();
});

it('searches teachers by profile, account, and college identifiers', function (string $searchTerm) {
    $college = College::query()->create([
        'college_code' => 'COL-SEARCH-01',
        'name' => 'Searchable College',
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $account = User::factory()->create(['name' => 'Account Search Name', 'email' => 'searchable@example.com', 'mobile_no' => '01711111111']);

    Teacher::query()->create([
        'user_id' => $account->id,
        'college_id' => $college->id,
        'name' => 'Profile Search Name',
        'ttis_id' => 'TTIS-SEARCH-01',
    ]);
    $account->update(['name' => 'Account Search Name']);
    Teacher::query()->create(['name' => 'Teacher Hidden From Search']);

    Livewire::test(TeacherManagement::class)
        ->set('search', "  {$searchTerm}  ")
        ->assertSee('Account Search Name')
        ->assertDontSee('Teacher Hidden From Search');
})->with([
    'profile name' => 'Profile Search',
    'linked account name' => 'Account Search',
    'TTIS ID' => 'TTIS-SEARCH',
    'mobile number' => '01711111111',
    'email address' => 'searchable@example.com',
    'college code' => 'COL-SEARCH',
    'college name' => 'Searchable College',
]);

it('clears teacher search and filters together', function () {
    Livewire::test(TeacherManagement::class)
        ->set('search', 'Teacher')
        ->set('subjectFilter', 'Physics')
        ->set('collegeCodeFilter', 'COL-001')
        ->assertSee('সক্রিয় সার্চ বা ফিল্টার অনুযায়ী ফলাফল দেখানো হচ্ছে।')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('subjectFilter', '')
        ->assertSet('collegeCodeFilter', '')
        ->assertDontSee('সক্রিয় সার্চ বা ফিল্টার অনুযায়ী ফলাফল দেখানো হচ্ছে।');
});

it('sorts teachers and the college code filter in either direction', function () {
    $lowerCodeCollege = College::query()->create(['college_code' => '100', 'name' => 'Lower Code College']);
    $higherCodeCollege = College::query()->create(['college_code' => '900', 'name' => 'Higher Code College']);
    Teacher::query()->create(['college_id' => $higherCodeCollege->id, 'name' => 'Higher Code Teacher']);
    Teacher::query()->create(['college_id' => $lowerCodeCollege->id, 'name' => 'Lower Code Teacher']);

    Livewire::test(TeacherManagement::class)
        ->assertSet('collegeCodeSort', 'asc')
        ->assertSeeInOrder(['100', '900'])
        ->assertSeeInOrder(['Lower Code Teacher', 'Higher Code Teacher'])
        ->set('collegeCodeSort', 'desc')
        ->assertSeeInOrder(['900', '100'])
        ->assertSeeInOrder(['Higher Code Teacher', 'Lower Code Teacher'])
        ->set('collegeCodeSort', 'invalid')
        ->assertSet('collegeCodeSort', 'asc');
});

it('gives principals a college-scoped teacher management interface', function () {
    $principalCollege = College::query()->create(['college_code' => 'OWN-001', 'name' => 'Principal College', 'approval_status' => ApprovalStatus::Approved]);
    $otherCollege = College::query()->create(['college_code' => 'OTHER-002', 'name' => 'Other College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->withRole('principal')->create(['college_id' => $principalCollege->id]);
    $physics = Subject::query()->create(['name' => 'Physics']);
    $chemistry = Subject::query()->create(['name' => 'Chemistry']);
    Teacher::query()->create(['college_id' => $principalCollege->id, 'name' => 'Own College Teacher', 'subject_id' => $physics->id]);
    Teacher::query()->create(['college_id' => $otherCollege->id, 'name' => 'Other College Teacher', 'subject_id' => $chemistry->id]);

    Livewire::actingAs($principal)->test(TeacherManagement::class)
        ->assertSee('আমার কলেজের শিক্ষক')
        ->assertSee('Own College Teacher')
        ->assertSee('Physics')
        ->assertDontSee('Other College Teacher')
        ->assertDontSee('Chemistry')
        ->assertDontSee('OTHER-002')
        ->assertDontSee('সব কলেজ কোড')
        ->assertDontSee('মোট 2টি কলেজ')
        ->assertDontSee('ডেটা ইম্পোর্ট')
        ->assertDontSee('ট্র্যাশ')
        ->set('collegeCodeFilter', 'OTHER-002')
        ->assertSet('collegeCodeFilter', '')
        ->set('search', 'Other College Teacher')
        ->assertDontSee('Other College Teacher');
});

it('keeps every row checkbox checked when selecting the current page', function () {
    Teacher::query()->create(['name' => 'First Selected Teacher']);
    Teacher::query()->create(['name' => 'Second Selected Teacher']);
    $expectedTeacherIds = Teacher::query()->latest()->pluck('id')->map(fn (int $id): string => (string) $id)->all();

    $component = Livewire::test(TeacherManagement::class)
        ->call('toggleSelectAllOnPage')
        ->assertSet('selectAllOnPage', true)
        ->assertSet('selectedTeacherIds', $expectedTeacherIds)
        ->assertDispatched('teacher-selection-updated', selected: true)
        ->assertSeeHtml('data-teacher-checkbox');

    expect(file_get_contents(resource_path('views/livewire/teacher-management.blade.php')))
        ->toMatch('/wire:click="toggleSelectAllOnPage"\s+data-teacher-checkbox/');

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
    $user = User::factory()->create(['email' => 'old-teacher@example.com', 'mobile_no' => '01799999991']);
    $oldCollege = College::query()->create(['college_code' => '100', 'name' => 'Old College']);
    $updatedCollege = College::query()->create(['college_code' => '200', 'name' => 'Updated College']);
    $designation = Designation::query()->create(['name' => 'Assistant Professor']);
    $subject = Subject::query()->create(['name' => 'Physics']);
    $teacherLevel = TeacherLevel::query()->create(['name' => 'College']);
    $employment = Employment::query()->create(['name' => 'Permanent']);
    $teacher = Teacher::query()->create([
        'user_id' => $user->id,
        'college_id' => $oldCollege->id,
        'name' => 'Old Name',
    ]);

    $updatedData = [
        'college_code' => '200',
        'college_name' => 'Updated College',
        'name' => 'Updated Teacher',
        'designation' => 'Assistant Professor',
        'subject' => 'Physics',
        'teacher_level' => 'College',
        'employment_type' => 'Permanent',
        'mobile_number' => '01700000000',
        'email' => 'teacher@example.com',
    ];

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('editForm', $updatedData)
        ->call('updateTeacher')
        ->assertHasNoErrors()
        ->assertDispatched('close-edit-modal');

    $teacher->refresh();

    expect($teacher->college_id)->toBe($updatedCollege->id)
        ->and($teacher->designation_id)->toBe($designation->id)
        ->and($teacher->subject_id)->toBe($subject->id)
        ->and($teacher->teacher_level_id)->toBe($teacherLevel->id)
        ->and($teacher->employment_id)->toBe($employment->id)
        ->and($teacher->name)->toBe('Updated Teacher')
        ->and($user->refresh()->mobile_no)->toBe('01700000000')
        ->and($user->email)->toBe('teacher@example.com');
});

it('shows user friendly validation errors while editing teacher data', function () {
    $teacher = Teacher::query()->create([
        'name' => 'Existing Teacher',
    ]);

    Livewire::test(TeacherManagement::class)
        ->call('editTeacher', $teacher->id)
        ->set('editForm.name', '')
        ->set('editForm.email', 'invalid-email')
        ->call('updateTeacher')
        ->assertHasErrors([
            'editForm.name' => 'required',
            'editForm.email' => 'email',
        ])
        ->assertSee('তথ্য আপডেট করা যায়নি')
        ->assertSee('শিক্ষকের নাম অবশ্যই দিতে হবে।')
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
        ->assertSee('শিক্ষকের তথ্য ট্র্যাশে পাঠাবেন?')
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
        ->assertSet('selectedTeacherIds', [])
        ->assertSet('selectAllOnPage', false)
        ->assertDispatched('teacher-selection-updated', selected: false);

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
        ->assertSet('selectedTeacherIds', [])
        ->assertSet('selectAllOnPage', false)
        ->assertDispatched('teacher-selection-updated', selected: false);

    expect(Teacher::query()->count())->toBe(2);
});

it('permanently deletes a teacher from the trash after confirmation', function () {
    $teacher = Teacher::query()->create(['name' => 'Permanently Deleted Teacher']);
    $teacher->delete();

    Livewire::test(TeacherManagement::class)
        ->call('toggleTrashed')
        ->call('confirmPermanentTeacherDeletion', $teacher->id)
        ->assertSet('deletingTeacherIds', [$teacher->id])
        ->assertSet('permanentDeletion', true)
        ->assertSee('শিক্ষকের তথ্য স্থায়ীভাবে মুছে ফেলবেন?')
        ->call('deleteTeacher');

    expect(Teacher::withTrashed()->find($teacher->id))->toBeNull();
});

it('permanently deletes multiple selected teachers from the trash', function () {
    $teachers = collect([
        Teacher::query()->create(['name' => 'First Permanent Teacher']),
        Teacher::query()->create(['name' => 'Second Permanent Teacher']),
    ]);

    $teachers->each->delete();

    Livewire::test(TeacherManagement::class)
        ->call('toggleTrashed')
        ->set('selectedTeacherIds', $teachers->pluck('id')->map(fn (int $id): string => (string) $id)->all())
        ->call('confirmBulkPermanentDeletion')
        ->assertSet('permanentDeletion', true)
        ->call('deleteTeacher')
        ->assertSet('selectedTeacherIds', []);

    expect(Teacher::withTrashed()->whereKey($teachers->pluck('id'))->count())->toBe(0);
});

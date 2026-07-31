<?php

use App\Livewire\TeacherDetails;
use App\Livewire\TeacherProfileForm;
use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\Teacher;
use App\Models\Thana;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\User;
use App\Enums\UserRole as Role;
use App\Enums\ApprovalStatus;
use Livewire\Livewire;

beforeEach(function () {
    $division = Division::query()->firstOrCreate(['name' => 'Teacher Test Division'], ['country_id' => 1, 'bn_name' => 'শিক্ষক টেস্ট বিভাগ']);
    $district = District::query()->firstOrCreate(['name' => 'Teacher Test District', 'division_id' => $division->id], ['bn_name' => 'শিক্ষক টেস্ট জেলা']);
    Thana::query()->firstOrCreate(['name' => 'Teacher Test Thana', 'district_id' => $district->id], ['bn_name' => 'শিক্ষক টেস্ট থানা']);
});

it('creates a teacher linked to a college with contact and bank information', function () {
    $college = College::query()->create(['code' => 'TC-1', 'name' => 'Teacher College']);
    $division = Division::query()->where('name', 'Teacher Test Division')->firstOrFail();
    $district = District::query()->where('name', 'Teacher Test District')->firstOrFail();
    $thana = Thana::query()->where('name', 'Teacher Test Thana')->firstOrFail();
    $institute = TrainingInstitute::query()->create(['name' => 'Profile Training Institute']);
    $training = TrainingType::query()->create(['training_institute_id' => $institute->id, 'name' => 'Profile ICT Training', 'duration_value' => 7, 'duration_unit' => 'days']);

    Livewire::actingAs(User::factory()->create(['role' => Role::Admin]))->test(TeacherProfileForm::class)
        ->set('collegeId', (string) $college->id)->set('name', 'New Teacher')->set('tmisId', 'TMIS-PROFILE')
        ->set('divisionId', (string) $division->id)->set('districtId', (string) $district->id)->set('thanaId', (string) $thana->id)
        ->set('presentAddress', 'Present Address')->set('permanentAddress', 'Permanent Address')
        ->set('mobileNumber', '01700000000')->set('email', 'profile@example.com')
        ->set('bankName', 'Sonali Bank')->set('bankBranchName', 'Main Branch')->set('bankRoutingNumber', '123456789')
        ->set('trainingEntries', [[
            'kind' => 'catalog', 'training_institute_id' => (string) $institute->id, 'institute_name' => '',
            'training_type_id' => (string) $training->id, 'name' => '', 'duration_value' => '',
            'duration_unit' => 'days', 'training_year' => '2026',
        ]])
        ->call('save')->assertHasNoErrors()->assertRedirect(route('teachers.manage'));

    $teacher = Teacher::query()->where('tmis_id', 'TMIS-PROFILE')->firstOrFail();
    expect($teacher->college_id)->toBe($college->id)
        ->and($teacher->college_name)->toBe('Teacher College')
        ->and($teacher->ttis_id)->toBe('TTIS-'.str_pad((string) $teacher->id, 8, '0', STR_PAD_LEFT))
        ->and($teacher->present_address)->toBe('Present Address')
        ->and($teacher->bank_name)->toBe('Sonali Bank')
        ->and($teacher->bank_routing_number)->toBe('123456789')
        ->and($teacher->trainingTypes)->toHaveCount(1)
        ->and($teacher->trainingTypes->first()->pivot->training_year)->toBe(2026);
});

it('keeps an existing TTIS ID unchanged when a teacher profile is edited', function () {
    $teacher = Teacher::query()->create(['name' => 'Imported Teacher', 'ttis_id' => 'LEGACY-TTIS-100']);

    expect($teacher->ttis_id)->toBe('LEGACY-TTIS-100');

    $teacher->update(['name' => 'Updated Imported Teacher']);

    expect($teacher->refresh()->ttis_id)->toBe('LEGACY-TTIS-100');
});

it('uses the linked user name as the canonical teacher display name', function () {
    $user = User::factory()->create(['name' => 'Account Name']);
    $teacher = Teacher::query()->create(['name' => 'Legacy Teacher Name', 'user_id' => $user->id]);

    $user->updateQuietly(['name' => 'Canonical Account Name']);

    expect($teacher->refresh()->display_name)->toBe('Canonical Account Name');
});

it('keeps the legacy teacher name available for imported teachers without accounts', function () {
    $teacher = Teacher::query()->create(['name' => 'Imported Teacher Without Account']);

    expect($teacher->display_name)->toBe('Imported Teacher Without Account');
});

it('submits a teacher profile under the selected college for principal approval', function () {
    $college = College::query()->create(['name' => 'Selected Teacher College', 'approval_status' => ApprovalStatus::Approved]);
    $division = Division::query()->where('name', 'Teacher Test Division')->firstOrFail();
    $district = District::query()->where('name', 'Teacher Test District')->firstOrFail();
    $thana = Thana::query()->where('name', 'Teacher Test Thana')->firstOrFail();
    $user = User::factory()->create(['role' => Role::Teacher, 'college_id' => $college->id, 'email' => 'registered-teacher@example.com']);

    Livewire::actingAs($user)->test(TeacherProfileForm::class)
        ->set('collegeId', (string) $college->id)
        ->set('name', 'Self Submitted Teacher')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('presentAddress', 'Present Address')
        ->set('permanentAddress', 'Permanent Address')
        ->set('mobileNumber', '01700000001')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $teacher = Teacher::query()->where('user_id', $user->id)->firstOrFail();
    expect($teacher->college_id)->toBe($college->id)
        ->and($teacher->approval_status)->toBe(ApprovalStatus::Pending)
        ->and($teacher->email)->toBe('registered-teacher@example.com')
        ->and($user->refresh()->teacher_id)->toBe($teacher->id)
        ->and($user->name)->toBe($teacher->name);
});

it('updates profile fields without changing existing institutional training history', function () {
    $college = College::query()->create(['name' => 'Training College']);
    $teacher = Teacher::query()->create(['name' => 'Trained Teacher', 'college_id' => $college->id]);
    $institute = TrainingInstitute::query()->create(['name' => 'Training Institute']);
    $training = TrainingType::query()->create(['training_institute_id' => $institute->id, 'name' => 'ICT Training', 'duration_value' => 5, 'duration_unit' => 'days']);
    $teacher->trainingTypes()->attach($training->id, ['training_year' => 2025]);
    $division = Division::query()->where('name', 'Teacher Test Division')->firstOrFail();
    $district = District::query()->where('name', 'Teacher Test District')->firstOrFail();
    $thana = Thana::query()->where('name', 'Teacher Test Thana')->firstOrFail();

    Livewire::actingAs(User::factory()->create(['role' => Role::Admin]))->test(TeacherProfileForm::class, ['teacher' => $teacher])
        ->assertSet('trainingEntries.0.training_type_id', (string) $training->id)
        ->assertSee('প্রতিষ্ঠানভিত্তিক ট্রেনিং ইতিহাস')
        ->set('divisionId', (string) $division->id)->set('districtId', (string) $district->id)->set('thanaId', (string) $thana->id)
        ->set('presentAddress', 'Updated Present')->set('permanentAddress', 'Updated Permanent')->set('mobileNumber', '01800000000')
        ->call('save')->assertHasNoErrors();

    expect($teacher->refresh()->trainingTypes)->toHaveCount(1)
        ->and($teacher->trainingTypes->first()->pivot->training_year)->toBe(2025);
});

it('shows all teacher profile sections on a dedicated details page', function () {
    $teacher = Teacher::query()->create(['name' => 'Details Teacher', 'present_address' => 'Teacher Present', 'permanent_address' => 'Teacher Permanent', 'mobile_number' => '01900000000', 'bank_name' => 'Agrani Bank', 'bank_branch_name' => 'Town Branch', 'bank_routing_number' => '987654321']);

    Livewire::actingAs(User::factory()->create(['role' => Role::Admin]))->test(TeacherDetails::class, ['teacher' => $teacher])
        ->assertSee('Details Teacher')->assertSee('Teacher Present')->assertSee('Teacher Permanent')
        ->assertSee('01900000000')->assertSee('Agrani Bank')->assertSee('Town Branch')->assertSee('987654321')
        ->assertSee('প্রতিষ্ঠানভিত্তিক ট্রেনিং ইতিহাস')
        ->assertDontSee('কম্পিউটার ল্যাব')
        ->assertDontSee('কম্পিউটার সংখ্যা');
});

it('protects teacher create edit and details pages with authentication', function () {
    $teacher = Teacher::query()->create(['name' => 'Protected Teacher']);
    $this->get(route('teachers.create'))->assertRedirect(route('login'));
    $this->get(route('teachers.edit', $teacher))->assertRedirect(route('login'));
    $this->get(route('teachers.show', $teacher))->assertRedirect(route('login'));

    $user = User::factory()->create();
    $this->actingAs($user)->get(route('teachers.create'))->assertSuccessful();
    $this->actingAs($user)->get(route('teachers.edit', $teacher))->assertSuccessful();
    $this->actingAs($user)->get(route('teachers.show', $teacher))->assertSuccessful();
});

it('allows a teacher to view and update their profile after principal approval', function () {
    $college = College::query()->create(['name' => 'Approved Profile College', 'approval_status' => ApprovalStatus::Approved]);
    $division = Division::query()->where('name', 'Teacher Test Division')->firstOrFail();
    $district = District::query()->where('name', 'Teacher Test District')->firstOrFail();
    $thana = Thana::query()->where('name', 'Teacher Test Thana')->firstOrFail();
    $principal = User::factory()->create(['role' => Role::Principal, 'college_id' => $college->id]);
    $user = User::factory()->create(['role' => Role::Teacher, 'college_id' => $college->id, 'email' => 'registered-teacher@example.com']);
    $teacher = Teacher::query()->create([
        'name' => 'Approved Self Service Teacher',
        'user_id' => $user->id,
        'college_id' => $college->id,
        'division_id' => $division->id,
        'district_id' => $district->id,
        'thana_id' => $thana->id,
        'present_address' => 'Old Present Address',
        'permanent_address' => 'Permanent Address',
        'mobile_number' => '01700000002',
        'approval_status' => ApprovalStatus::Approved,
        'approved_by' => $principal->id,
        'approved_at' => now(),
    ]);
    $user->update(['teacher_id' => $teacher->id]);

    $this->actingAs($user)->get(route('teachers.show', $teacher))
        ->assertSuccessful()
        ->assertSee('Approved Self Service Teacher')
        ->assertSee('সম্পাদনা')
        ->assertSee(route('teachers.edit', $teacher), false)
        ->assertSee('ড্যাশবোর্ডে ফিরুন');

    Livewire::actingAs($user)->test(TeacherProfileForm::class, ['teacher' => $teacher])
        ->assertSet('email', 'registered-teacher@example.com')
        ->assertSee('শিক্ষক account তৈরির সময় ব্যবহৃত ইমেইল ঠিকানা।')
        ->set('presentAddress', 'Updated Present Address')
        ->set('email', 'tampered@example.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect($teacher->refresh()->present_address)->toBe('Updated Present Address')
        ->and($teacher->email)->toBe('registered-teacher@example.com')
        ->and($teacher->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher->approved_by)->toBe($principal->id)
        ->and($teacher->approved_at)->not->toBeNull();
});

it('does not allow a teacher to edit a profile before it is approved', function (ApprovalStatus $status) {
    $user = User::factory()->create(['role' => Role::Teacher]);
    $teacher = Teacher::query()->create([
        'name' => 'Unapproved Teacher',
        'user_id' => $user->id,
        'approval_status' => $status,
    ]);
    $user->update(['teacher_id' => $teacher->id]);

    $this->actingAs($user)->get(route('teachers.edit', $teacher))->assertForbidden();
})->with([
    'pending' => ApprovalStatus::Pending,
    'rejected' => ApprovalStatus::Rejected,
]);

<?php

use App\Enums\ApprovalStatus;
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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role as PermissionRole;

beforeEach(function () {
    $division = Division::query()->firstOrCreate(['name' => 'Teacher Test Division'], ['country_id' => 1, 'bn_name' => 'শিক্ষক টেস্ট বিভাগ']);
    $district = District::query()->firstOrCreate(['name' => 'Teacher Test District', 'division_id' => $division->id], ['bn_name' => 'শিক্ষক টেস্ট জেলা']);
    Thana::query()->firstOrCreate(['name' => 'Teacher Test Thana', 'district_id' => $district->id], ['bn_name' => 'শিক্ষক টেস্ট থানা']);
});

it('creates a teacher linked to a college with contact and bank information', function () {
    $college = College::query()->create(['college_code' => 'TC-1', 'name' => 'Teacher College']);
    $division = Division::query()->where('name', 'Teacher Test Division')->firstOrFail();
    $district = District::query()->where('name', 'Teacher Test District')->firstOrFail();
    $thana = Thana::query()->where('name', 'Teacher Test Thana')->firstOrFail();
    $institute = TrainingInstitute::query()->create(['name' => 'Profile Training Institute']);
    $training = TrainingType::query()->create(['training_institute_id' => $institute->id, 'name' => 'Profile ICT Training', 'duration_value' => 7, 'duration_unit' => 'days']);

    Storage::fake('public');

    Livewire::actingAs(User::factory()->withRole('admin')->create())->test(TeacherProfileForm::class)
        ->set('collegeId', (string) $college->id)->set('name', 'New Teacher')
        ->set('accountEmail', 'new-teacher-account@example.com')->set('accountPassword', 'password')->set('accountPassword_confirmation', 'password')
        ->set('divisionId', (string) $division->id)->set('districtId', (string) $district->id)->set('thanaId', (string) $thana->id)
        ->set('presentAddress', 'Present Address')->set('permanentAddress', 'Permanent Address')
        ->set('mobileNumber', '01700000000')->set('email', 'profile@example.com')
        ->set('profileImage', UploadedFile::fake()->image('profile.jpg'))
        ->set('digitalSignature', UploadedFile::fake()->image('signature.png'))
        ->set('bankName', 'Sonali Bank')->set('bankBranchName', 'Main Branch')->set('bankAccountNumber', '1234567890123')->set('bankRoutingNumber', '123456789')
        ->set('trainingEntries', [[
            'kind' => 'catalog', 'training_institute_id' => (string) $institute->id, 'institute_name' => '',
            'training_type_id' => (string) $training->id, 'name' => '', 'duration_value' => '',
            'duration_unit' => 'days', 'training_year' => '2026',
        ]])
        ->call('submit')->assertHasNoErrors()->assertRedirect(route('teachers.manage'));

    $teacher = Teacher::query()->where('name', 'New Teacher')->firstOrFail();
    expect($teacher->college_id)->toBe($college->id)
        ->and($teacher->college->name)->toBe('Teacher College')
        ->and($teacher->ttis_id)->toMatch('/^\d{6}$/')
        ->and($teacher->present_address)->toBe('Present Address')
        ->and($teacher->bank_name)->toBe('Sonali Bank')
        ->and($teacher->bank_account_number)->toBe('1234567890123')
        ->and($teacher->bank_routing_number)->toBe('123456789')
        ->and($teacher->trainingTypes)->toHaveCount(1)
        ->and($teacher->trainingTypes->first()->pivot->training_year)->toBe(2026)
        ->and($teacher->user?->email)->toBe('new-teacher-account@example.com')
        ->and($teacher->user?->mobile_no)->toBe('01700000000')
        ->and($teacher->user?->picture)->toStartWith('profile-images/')
        ->and($teacher->user?->digital_signature)->toStartWith('signatures/');
});


it('generates unique six digit TTIS IDs for new teacher profiles', function () {
    $teachers = collect(range(1, 100))->map(fn (int $index): Teacher => Teacher::query()->create(['name' => "Generated TTIS Teacher {$index}"]));

    expect($teachers->pluck('ttis_id')->unique())->toHaveCount(100);

    $teachers->each(function (Teacher $teacher): void {
        expect($teacher->ttis_id)->toMatch('/^\d{6}$/');
    });
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
    $user = User::factory()->withRole('teacher')->create(['college_id' => $college->id, 'email' => 'registered-teacher@example.com', 'mobile_no' => '01799999999']);

    Livewire::actingAs($user)->test(TeacherProfileForm::class)
        ->set('collegeId', (string) $college->id)
        ->set('name', 'Self Submitted Teacher')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('presentAddress', 'Present Address')
        ->set('permanentAddress', 'Permanent Address')
        ->set('mobileNumber', '01700000001')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $teacher = Teacher::query()->where('user_id', $user->id)->firstOrFail();
    expect($teacher->college_id)->toBe($college->id)
        ->and($teacher->ttis_id)->toMatch('/^\d{6}$/')
        ->and($teacher->approval_status)->toBe(ApprovalStatus::Pending)
        ->and($teacher->user?->email)->toBe('registered-teacher@example.com')
        ->and($user->refresh()->teacherProfile?->is($teacher))->toBeTrue()
        ->and($user->name)->toBe($teacher->name)
        ->and($user->refresh()->mobile_no)->toBe('01700000001');
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

    Livewire::actingAs(User::factory()->withRole('admin')->create())->test(TeacherProfileForm::class, ['teacher' => $teacher])
        ->assertSet('trainingEntries.0.training_type_id', (string) $training->id)
        ->assertSee('প্রতিষ্ঠানভিত্তিক ট্রেনিং ইতিহাস')
        ->set('divisionId', (string) $division->id)->set('districtId', (string) $district->id)->set('thanaId', (string) $thana->id)
        ->set('presentAddress', 'Updated Present')->set('permanentAddress', 'Updated Permanent')->set('mobileNumber', '01800000000')
        ->call('save')->assertHasNoErrors();

    expect($teacher->refresh()->trainingTypes)->toHaveCount(1)
        ->and($teacher->trainingTypes->first()->pivot->training_year)->toBe(2025);
});

it('allows an admin to update a teacher without location or address details', function () {
    $college = College::query()->create(['name' => 'Optional Address College']);
    $teacherAccount = User::factory()->create([
        'college_id' => $college->id,
        'email' => 'optional-address@example.com',
        'mobile_no' => '01811111111',
    ]);
    $teacher = Teacher::query()->create([
        'user_id' => $teacherAccount->id,
        'college_id' => $college->id,
        'name' => 'Teacher Without Address',
    ]);

    Livewire::actingAs(User::factory()->withRole('admin')->create())
        ->test(TeacherProfileForm::class, ['teacher' => $teacher])
        ->set('name', 'Updated Teacher Without Address')
        ->call('save')
        ->assertHasNoErrors([
            'divisionId',
            'districtId',
            'thanaId',
            'presentAddress',
            'permanentAddress',
        ]);

    expect($teacher->refresh()->name)->toBe('Updated Teacher Without Address')
        ->and($teacher->division_id)->toBeNull()
        ->and($teacher->district_id)->toBeNull()
        ->and($teacher->thana_id)->toBeNull()
        ->and($teacher->present_address)->toBeNull()
        ->and($teacher->permanent_address)->toBeNull();
});

it('shows all teacher profile sections on a dedicated details page', function () {
    $user = User::factory()->create(['email' => 'details-teacher@example.com', 'mobile_no' => '01900000000']);
    $teacher = Teacher::query()->create(['name' => 'Details Teacher', 'user_id' => $user->id, 'present_address' => 'Teacher Present', 'permanent_address' => 'Teacher Permanent', 'bank_name' => 'Agrani Bank', 'bank_branch_name' => 'Town Branch', 'bank_account_number' => '9876543210123', 'bank_routing_number' => '987654321']);

    Livewire::actingAs(User::factory()->withRole('admin')->create())->test(TeacherDetails::class, ['teacher' => $teacher])
        ->assertSee('Details Teacher')->assertSee('Teacher Present')->assertSee('Teacher Permanent')
        ->assertSee('01900000000')->assertSee('Agrani Bank')->assertSee('Town Branch')->assertSee('9876543210123')->assertSee('987654321')
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
    $principal = User::factory()->withRole('principal')->create(['college_id' => $college->id]);
    $user = User::factory()->withRole('teacher')->create(['college_id' => $college->id, 'email' => 'registered-teacher@example.com', 'mobile_no' => '01799999999']);
    $teacher = Teacher::query()->create([
        'name' => 'Approved Self Service Teacher',
        'user_id' => $user->id,
        'college_id' => $college->id,
        'division_id' => $division->id,
        'district_id' => $district->id,
        'thana_id' => $thana->id,
        'present_address' => 'Old Present Address',
        'permanent_address' => 'Permanent Address',
                'approval_status' => ApprovalStatus::Approved,
        'approved_by' => $principal->id,
        'approved_at' => now(),
    ]);

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
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect($teacher->refresh()->present_address)->toBe('Updated Present Address')
        ->and($teacher->user?->email)->toBe('registered-teacher@example.com')
        ->and($teacher->approval_status)->toBe(ApprovalStatus::Approved)
        ->and($teacher->approved_by)->toBe($principal->id)
        ->and($teacher->approved_at)->not->toBeNull();
});

it('does not allow a teacher to edit a profile before it is approved', function (ApprovalStatus $status) {
    $user = User::factory()->withRole('teacher')->create();
    $teacher = Teacher::query()->create([
        'name' => 'Unapproved Teacher',
        'user_id' => $user->id,
        'approval_status' => $status,
    ]);

    $this->actingAs($user)->get(route('teachers.edit', $teacher))->assertForbidden();
})->with([
    'pending' => ApprovalStatus::Pending,
    'rejected' => ApprovalStatus::Rejected,
]);

it('enforces teacher profile permissions on routes and components', function () {
    $user = User::factory()->withRole('teacher')->create();
    $teacher = Teacher::query()->create([
        'name' => 'Permission Restricted Teacher',
        'user_id' => $user->id,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    PermissionRole::findByName('teacher')->revokePermissionTo('teachers.update');

    $this->actingAs($user)->get(route('teachers.edit', $teacher))->assertForbidden();

    Livewire::actingAs($user)->test(TeacherDetails::class, ['teacher' => $teacher])
        ->assertDontSee(route('teachers.edit', $teacher), false)
        ->assertDontSee('সম্পাদনা');

    Livewire::actingAs($user)->test(TeacherProfileForm::class, ['teacher' => $teacher])
        ->assertForbidden();
});

it('reports required fields when an incomplete teacher profile is submitted', function () {
    $user = User::factory()->withRole('teacher')->create();

    Livewire::actingAs($user)->test(TeacherProfileForm::class)
        ->call('submit')
        ->assertSet('activeStep', 'basic')
        ->assertSet('submissionError', true)
        ->assertSee('প্রয়োজনীয় তথ্য পাওয়া যায়নি')
        ->assertHasErrors([
            'collegeId' => 'required',
            'divisionId' => 'required',
            'districtId' => 'required',
            'thanaId' => 'required',
            'presentAddress' => 'required',
            'permanentAddress' => 'required',
        ]);

    expect($user->teacherProfile)->toBeNull();
});

it('renders and navigates every teacher profile form step', function () {
    $user = User::factory()->withRole('teacher')->create();

    Livewire::actingAs($user)->test(TeacherProfileForm::class)
        ->assertSet('activeStep', 'basic')
        ->assertSee('Basic Teacher Details')
        ->call('goToStep', 'professional')
        ->assertSet('activeStep', 'professional')
        ->assertSee('Professional Details')
        ->call('goToStep', 'contact')
        ->assertSet('activeStep', 'contact')
        ->assertSee('Contact & Address')
        ->call('goToStep', 'training')
        ->assertSet('activeStep', 'training')
        ->assertSee('Training History')
        ->call('goToStep', 'bank')
        ->assertSet('activeStep', 'bank')
        ->assertSee('Bank Details');
});

it('allows a teacher to correct and resubmit a rejected profile', function () {
    $college = College::query()->create(['name' => 'Rejected Profile College', 'approval_status' => ApprovalStatus::Approved]);
    $division = Division::query()->where('name', 'Teacher Test Division')->firstOrFail();
    $district = District::query()->where('name', 'Teacher Test District')->firstOrFail();
    $thana = Thana::query()->where('name', 'Teacher Test Thana')->firstOrFail();
    $admin = User::factory()->withRole('admin')->create();
    $user = User::factory()->withRole('teacher')->create(['college_id' => $college->id]);
    $teacher = Teacher::query()->create([
        'user_id' => $user->id,
        'college_id' => $college->id,
        'name' => 'Rejected Teacher',
        'division_id' => $division->id,
        'district_id' => $district->id,
        'thana_id' => $thana->id,
        'present_address' => 'Old present address',
        'permanent_address' => 'Permanent address',
        'approval_status' => ApprovalStatus::Rejected,
        'approved_by' => $admin->id,
        'approved_at' => now(),
    ]);

    PermissionRole::findByName('teacher')->revokePermissionTo('teachers.update');

    $this->actingAs($user)->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('প্রত্যাখ্যাত')
        ->assertDontSee('অনুমোদনের জন্য অপেক্ষারত')
        ->assertSee('প্রোফাইল সংশোধন ও পুনরায় জমা দিন')
        ->assertSee(route('teachers.resubmit', $teacher), false);
    $this->actingAs($user)->get(route('teachers.resubmit', $teacher))->assertSuccessful();

    Livewire::actingAs($user)->test(TeacherProfileForm::class, ['teacher' => $teacher])
        ->assertSet('editingId', $teacher->id)
        ->set('presentAddress', 'Corrected present address')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect($teacher->refresh()->approval_status)->toBe(ApprovalStatus::Pending)
        ->and($teacher->present_address)->toBe('Corrected present address')
        ->and($teacher->approved_by)->toBeNull()
        ->and($teacher->approved_at)->toBeNull()
        ->and($user->unreadNotifications()->firstOrFail()->data['status'])->toBe(ApprovalStatus::Pending->value);
});

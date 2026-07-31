<?php

use App\Enums\ApprovalStatus;
use App\Livewire\CollegeForm;
use App\Livewire\CollegeManagement;
use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $firstDivision = Division::query()->firstOrCreate(['name' => 'Test Division One'], ['country_id' => 1, 'bn_name' => 'টেস্ট বিভাগ এক']);
    $secondDivision = Division::query()->firstOrCreate(['name' => 'Test Division Two'], ['country_id' => 1, 'bn_name' => 'টেস্ট বিভাগ দুই']);
    District::query()->firstOrCreate(['name' => 'Test District One', 'division_id' => $firstDivision->id], ['bn_name' => 'টেস্ট জেলা এক']);
    District::query()->firstOrCreate(['name' => 'Test District Two', 'division_id' => $secondDivision->id], ['bn_name' => 'টেস্ট জেলা দুই']);
    District::query()->get()->each(fn (District $district) => Thana::query()->firstOrCreate(['name' => "Test Thana {$district->id}", 'district_id' => $district->id], ['bn_name' => "টেস্ট থানা {$district->id}"]));
});

it('supports searchable soft-deleted colleges with Flux confirmation', function () {
    expect(Schema::hasColumn('colleges', 'deleted_at'))->toBeTrue();

    $college = College::query()->create([
        'name' => 'Searchable College',
        'code' => 'SEARCH-101',
        'principal_name' => 'Professor Search',
        'address' => 'Search Road',
    ]);

    Livewire::test(CollegeManagement::class)
        ->set('search', 'Professor Search')
        ->assertSee('Searchable College')
        ->call('confirmDeletion', $college->id)
        ->assertSet('deletingCollegeIds', [$college->id])
        ->assertSee('কলেজ ট্র্যাশে পাঠাবেন?')
        ->call('deleteConfirmed');

    expect(College::query()->find($college->id))->toBeNull()
        ->and(College::withTrashed()->find($college->id))->not->toBeNull();

    Livewire::test(CollegeManagement::class)
        ->call('toggleTrashed')
        ->assertSee('Searchable College')
        ->call('restore', $college->id);

    expect($college->fresh())->not->toBeNull();
});

it('searches colleges by location and filters by type and approval status', function () {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $district = District::query()->whereBelongsTo($division)->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($district)->firstOrFail();

    College::query()->create([
        'name' => 'Matching Government College',
        'division_id' => $division->id,
        'district_id' => $district->id,
        'thana_id' => $thana->id,
        'college_type' => 'government',
        'approval_status' => ApprovalStatus::Approved,
    ]);
    College::query()->create([
        'name' => 'Hidden Private College',
        'college_type' => 'non_government',
        'approval_status' => ApprovalStatus::Pending,
    ]);

    Livewire::test(CollegeManagement::class)
        ->set('search', $district->name)
        ->set('collegeTypeFilter', 'government')
        ->set('approvalStatusFilter', ApprovalStatus::Approved->value)
        ->assertSee('Matching Government College')
        ->assertDontSee('Hidden Private College')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('collegeTypeFilter', '')
        ->assertSet('approvalStatusFilter', '');
});

it('soft deletes and restores selected colleges in groups', function () {
    $colleges = collect([
        College::query()->create(['name' => 'First Group College']),
        College::query()->create(['name' => 'Second Group College']),
    ]);
    $selectedIds = $colleges->pluck('id')->map(fn (int $id): string => (string) $id)->all();

    Livewire::test(CollegeManagement::class)
        ->set('selectedCollegeIds', $selectedIds)
        ->call('confirmBulkDeletion')
        ->assertSet('deletingCollegeIds', $colleges->pluck('id')->all())
        ->assertSee('নির্বাচিত 2টি কলেজ')
        ->call('deleteConfirmed')
        ->assertSet('selectedCollegeIds', []);

    expect(College::query()->whereKey($colleges->pluck('id'))->count())->toBe(0)
        ->and(College::onlyTrashed()->whereKey($colleges->pluck('id'))->count())->toBe(2);

    Livewire::test(CollegeManagement::class)
        ->call('toggleTrashed')
        ->set('selectedCollegeIds', $selectedIds)
        ->call('restoreSelected');

    expect(College::query()->whereKey($colleges->pluck('id'))->count())->toBe(2);
});

it('permanently deletes trashed colleges only after Flux confirmation', function () {
    $college = College::query()->create(['name' => 'Permanent College']);
    $college->delete();

    Livewire::test(CollegeManagement::class)
        ->call('toggleTrashed')
        ->call('confirmPermanentDeletion', $college->id)
        ->assertSet('permanentDeletion', true)
        ->assertSee('কলেজ স্থায়ীভাবে মুছে ফেলবেন?')
        ->call('deleteConfirmed');

    expect(College::withTrashed()->find($college->id))->toBeNull()
        ->and(file_get_contents(resource_path('views/livewire/college-management.blade.php')))->not->toContain('wire:confirm');
});

it('requires authentication to manage colleges', function () {
    auth()->logout();
    $this->get(route('colleges.manage'))->assertRedirect(route('login'));
});

it('stores a complete college profile with multiple academic programs', function () {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $district = District::query()->whereBelongsTo($division)->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($district)->firstOrFail();

    Livewire::test(CollegeForm::class)
        ->set('code', '1201')
        ->set('name', 'Professional College')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('address', 'College Road')
        ->set('principalName', 'Professor Rahman')
        ->set('collegeEmail', 'info@professional.edu.bd')
        ->set('collegeWebsite', 'https://professional.edu.bd')
        ->set('collegeType', 'government')
        ->set('hasComputerLab', '1')
        ->set('labEquipmentType', 'both')
        ->set('desktopCount', '25')
        ->set('laptopCount', '10')
        ->set('programs', [
            ['level' => 'degree', 'names' => ['BA', 'BSc'], 'new_name' => ''],
            ['level' => 'honours', 'names' => ['বাংলা'], 'new_name' => ''],
            ['level' => 'masters', 'names' => ['ইংরেজি'], 'new_name' => ''],
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('colleges.manage'));

    $college = College::query()->where('code', '1201')->firstOrFail();
    expect($college->principal_name)->toBe('Professor Rahman')
        ->and($college->college_email)->toBe('info@professional.edu.bd')
        ->and($college->college_website)->toBe('https://professional.edu.bd')
        ->and($college->college_type)->toBe('government')
        ->and($college->has_computer_lab)->toBeTrue()
        ->and($college->lab_equipment_type)->toBe('both')
        ->and($college->desktop_count)->toBe(25)
        ->and($college->laptop_count)->toBe(10)
        ->and($college->programs)->toHaveCount(3)
        ->and($college->programs->firstWhere('level', 'degree')->items)->toBe(['BA', 'BSc'])
        ->and($college->programs->firstWhere('level', 'honours')->items)->toBe(['বাংলা']);
});

it('adds degree courses and honours subjects as unique tags', function () {
    \App\Models\Subject::query()->create(['name' => 'বাংলা']);

    Livewire::test(CollegeForm::class)
        ->assertSee('BA')
        ->assertSee('বাংলা')
        ->assertSee('Enter চাপলেই সেটি নিচে pill হিসেবে যুক্ত হবে')
        ->assertSee('লিখে Enter চাপুন')
        ->assertDontSee('ট্যাগ যোগ')
        ->assertSeeHtml('data-program-pillbox')
        ->call('addProgram')
        ->set('programs.0.new_name', 'BA')
        ->call('addProgramTag', 0)
        ->set('programs.0.new_name', 'ba')
        ->call('addProgramTag', 0)
        ->assertSet('programs.0.names', ['BA'])
        ->call('addProgram')
        ->set('programs.1.level', 'honours')
        ->set('programs.1.new_name', 'বাংলা')
        ->call('addProgramTag', 1)
        ->assertSet('programs.1.names', ['বাংলা'])
        ->call('removeProgramTag', 0, 0)
        ->assertSet('programs.0.names', []);
});

it('rejects a district and thana outside the selected administrative hierarchy', function () {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $unrelatedDistrict = District::query()->where('name', 'Test District Two')->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($unrelatedDistrict)->firstOrFail();

    Livewire::test(CollegeForm::class)
        ->set('name', 'Invalid Location College')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $unrelatedDistrict->id)
        ->set('thanaId', (string) $thana->id)
        ->set('address', 'Address')
        ->set('principalName', 'Principal')
        ->set('collegeType', 'other')
        ->set('hasComputerLab', '0')
        ->call('save')
        ->assertHasErrors(['districtId']);
});

it('requires device counts when a college has a computer lab', function () {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $district = District::query()->whereBelongsTo($division)->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($district)->firstOrFail();

    Livewire::test(CollegeForm::class)
        ->set('name', 'Lab College')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('address', 'Lab Road')
        ->set('principalName', 'Lab Principal')
        ->set('collegeType', 'government')
        ->set('hasComputerLab', '1')
        ->set('labEquipmentType', 'both')
        ->call('save')
        ->assertHasErrors(['desktopCount', 'laptopCount']);
});

it('supports a lab containing only one device category', function (string $equipmentType, string $countProperty, string $storedColumn, string $emptyColumn) {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $district = District::query()->whereBelongsTo($division)->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($district)->firstOrFail();

    Livewire::test(CollegeForm::class)
        ->set('name', "{$equipmentType} Only College")
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('address', 'Device Road')
        ->set('principalName', 'Principal')
        ->set('collegeType', 'government')
        ->set('hasComputerLab', '1')
        ->set('labEquipmentType', $equipmentType)
        ->set($countProperty, '12')
        ->call('save')
        ->assertHasNoErrors();

    $college = College::query()->where('name', "{$equipmentType} Only College")->firstOrFail();
    expect($college->{$storedColumn})->toBe(12)
        ->and($college->{$emptyColumn})->toBeNull();
})->with([
    'desktop only' => ['desktop', 'desktopCount', 'desktop_count', 'laptop_count'],
    'laptop only' => ['laptop', 'laptopCount', 'laptop_count', 'desktop_count'],
]);

it('stores null device counts when a college does not have a lab', function () {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $district = District::query()->whereBelongsTo($division)->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($district)->firstOrFail();

    Livewire::test(CollegeForm::class)
        ->set('name', 'No Lab College')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('address', 'No Lab Road')
        ->set('principalName', 'Principal')
        ->set('collegeType', 'non_government')
        ->set('hasComputerLab', '0')
        ->call('save')
        ->assertHasNoErrors();

    $college = College::query()->where('name', 'No Lab College')->firstOrFail();
    expect($college->has_computer_lab)->toBeFalse()
        ->and($college->desktop_count)->toBeNull()
        ->and($college->laptop_count)->toBeNull();
});

it('allows authenticated users to open the college management page', function () {
    $user = User::factory()->create();
    $college = College::query()->create(['name' => 'Editable College']);

    $this->actingAs($user)->get(route('colleges.manage'))->assertSuccessful()->assertSee('নতুন কলেজ তৈরি');
    $this->actingAs($user)->get(route('colleges.create'))->assertSuccessful()->assertSee('নতুন কলেজ তৈরি');
    $this->actingAs($user)->get(route('colleges.edit', $college))->assertSuccessful()->assertSee('কলেজ সম্পাদনা');

    Livewire::test(CollegeForm::class, ['college' => $college])
        ->assertSet('editingId', $college->id)
        ->assertSet('name', 'Editable College');
});

it('shows a concise college table and a separate full details page', function () {
    $college = College::query()->create([
        'name' => 'Details College',
        'code' => 'DETAIL-1',
        'address' => 'Complete College Address',
        'principal_name' => 'Principal Details',
        'college_email' => 'details@example.edu.bd',
        'college_website' => 'https://details.example.edu.bd',
        'has_computer_lab' => true,
        'lab_equipment_type' => 'both',
        'desktop_count' => 20,
        'laptop_count' => 5,
    ]);
    $college->programs()->create(['level' => 'degree', 'name' => 'BA', 'items' => ['BA', 'BSS']]);

    Livewire::test(CollegeManagement::class)
        ->assertSee('Details College')
        ->assertSee('দেখুন')
        ->assertDontSee('Complete College Address')
        ->assertDontSee('Principal Details')
        ->assertDontSee('BSS');

    $this->actingAs(User::factory()->create())
        ->get(route('colleges.show', $college))
        ->assertSuccessful()
        ->assertSee('Complete College Address')
        ->assertSee('Principal Details')
        ->assertSee('details@example.edu.bd')
        ->assertSee('https://details.example.edu.bd')
        ->assertSee('ডেস্কটপ')
        ->assertSee('20')
        ->assertSee('ল্যাপটপ')
        ->assertSee('5')
        ->assertSee('BA')
        ->assertSee('BSS');
});

it('gives a principal direct view and edit access to only their college profile', function () {
    $college = College::query()->create(['name' => 'Principal Profile College', 'approval_status' => ApprovalStatus::Approved]);
    $otherCollege = College::query()->create(['name' => 'Restricted College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create([
        'role' => \App\Enums\UserRole::Principal,
        'college_id' => $college->id,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    $this->actingAs($principal)->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('কলেজ প্রোফাইল')
        ->assertSee(route('colleges.show', $college), false)
        ->assertDontSee('কলেজ ব্যবস্থাপনা');

    $this->actingAs($principal)->get(route('colleges.show', $college))
        ->assertSuccessful()
        ->assertSee('Principal Profile College')
        ->assertSee('সম্পাদনা')
        ->assertSee(route('colleges.edit', $college), false)
        ->assertSee('ড্যাশবোর্ডে ফিরুন');

    $this->actingAs($principal)->get(route('colleges.edit', $college))->assertSuccessful();
    $this->actingAs($principal)->get(route('colleges.show', $otherCollege))->assertForbidden();
    $this->actingAs($principal)->get(route('colleges.edit', $otherCollege))->assertForbidden();
    $this->actingAs($principal)->get(route('colleges.manage'))->assertForbidden();
});

<?php

use App\Livewire\CollegeManagement;
use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\Thana;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $firstDivision = Division::query()->firstOrCreate(['name' => 'Test Division One'], ['country_id' => 1, 'bn_name' => 'টেস্ট বিভাগ এক']);
    $secondDivision = Division::query()->firstOrCreate(['name' => 'Test Division Two'], ['country_id' => 1, 'bn_name' => 'টেস্ট বিভাগ দুই']);
    District::query()->firstOrCreate(['name' => 'Test District One', 'division_id' => $firstDivision->id], ['bn_name' => 'টেস্ট জেলা এক']);
    District::query()->firstOrCreate(['name' => 'Test District Two', 'division_id' => $secondDivision->id], ['bn_name' => 'টেস্ট জেলা দুই']);
    District::query()->get()->each(fn (District $district) => Thana::query()->firstOrCreate(['name' => "Test Thana {$district->id}", 'district_id' => $district->id], ['bn_name' => "টেস্ট থানা {$district->id}"]));
});

it('requires authentication to manage colleges', function () {
    $this->get(route('colleges.manage'))->assertRedirect(route('login'));
});

it('stores a complete college profile with multiple academic programs', function () {
    $division = Division::query()->where('name', 'Test Division One')->firstOrFail();
    $district = District::query()->whereBelongsTo($division)->firstOrFail();
    $thana = Thana::query()->whereBelongsTo($district)->firstOrFail();

    Livewire::test(CollegeManagement::class)
        ->set('code', '1201')
        ->set('name', 'Professional College')
        ->set('divisionId', (string) $division->id)
        ->set('districtId', (string) $district->id)
        ->set('thanaId', (string) $thana->id)
        ->set('address', 'College Road')
        ->set('principalName', 'Professor Rahman')
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
        ->assertHasNoErrors();

    $college = College::query()->where('code', '1201')->firstOrFail();
    expect($college->principal_name)->toBe('Professor Rahman')
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

    Livewire::test(CollegeManagement::class)
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

    Livewire::test(CollegeManagement::class)
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

    Livewire::test(CollegeManagement::class)
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

    Livewire::test(CollegeManagement::class)
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

    Livewire::test(CollegeManagement::class)
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
    $this->actingAs(User::factory()->create())->get(route('colleges.manage'))->assertSuccessful();
});

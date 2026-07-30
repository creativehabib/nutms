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
        ->set('programs', [
            ['level' => 'degree', 'name' => 'BA'],
            ['level' => 'degree', 'name' => 'BSc'],
            ['level' => 'honours', 'name' => 'বাংলা'],
            ['level' => 'masters', 'name' => 'ইংরেজি'],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $college = College::query()->where('code', '1201')->firstOrFail();
    expect($college->principal_name)->toBe('Professor Rahman')
        ->and($college->college_type)->toBe('government')
        ->and($college->programs)->toHaveCount(4);
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
        ->call('save')
        ->assertHasErrors(['districtId']);
});

it('allows authenticated users to open the college management page', function () {
    $this->actingAs(User::factory()->create())->get(route('colleges.manage'))->assertSuccessful();
});

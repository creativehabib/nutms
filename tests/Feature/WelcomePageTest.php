<?php

use App\Enums\ApprovalStatus;
use App\Livewire\Frontend\AffiliatedCollegeDirectory;
use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\Teacher;
use App\Models\Training;
use Livewire\Livewire;

it('renders live platform statistics and the next training', function () {
    College::query()->create([
        'college_code' => '1001',
        'name' => 'Dynamic College',
        'is_active' => true,
    ]);
    Teacher::query()->create(['name' => 'Dynamic Teacher']);
    Training::factory()->create([
        'title' => 'Dynamic Teacher Training',
        'capacity' => 50,
        'status' => 'Upcoming',
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Dynamic Teacher Training')
        ->assertSee('১')
        ->assertSee('নিবন্ধিত শিক্ষক')
        ->assertSee('প্রশিক্ষণ কার্যক্রম')
        ->assertSee('অধিভুক্ত কলেজ')
        ->assertSee('৫০ জন');
});

it('lets public users browse affiliated colleges and their subjects', function () {
    $college = College::query()->create([
        'college_code' => '2020',
        'name' => 'Public Affiliated College',
        'principal_name' => 'Public Principal',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $college->programs()->create([
        'level' => 'honours',
        'name' => 'Honours',
        'items' => ['বাংলা', 'ইতিহাস'],
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Public Affiliated College')
        ->assertSee('বাংলা')
        ->assertSee('প্রধান নেভিগেশন')
        ->assertSee(route('public.colleges.show', $college));

    $this->get(route('public.colleges.index'))
        ->assertOk()
        ->assertSee('Public Affiliated College')
        ->assertSee('বাংলা')
        ->assertSee('প্রধান নেভিগেশন')
        ->assertSee('অধিভুক্ত কলেজ ডিরেক্টরি');

    Livewire::test(AffiliatedCollegeDirectory::class)
        ->set('search', 'ইতিহাস')
        ->assertSee('Public Affiliated College')
        ->set('search', 'Not available')
        ->assertDontSee('Public Affiliated College');

    $this->get(route('public.colleges.show', $college))
        ->assertOk()
        ->assertSee('Public Principal')
        ->assertSee('বাংলা')
        ->assertSee('প্রধান নেভিগেশন')
        ->assertSee('ইতিহাস');
});

it('only exposes active approved colleges to public users', function () {
    $college = College::query()->create([
        'name' => 'Pending Private College',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Pending,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Pending Private College');

    $this->get(route('public.colleges.show', $college))->assertNotFound();
});

it('filters public colleges by type division and district', function () {
    $firstDivision = Division::query()->create([
        'country_id' => 1,
        'name' => 'Public First Division',
        'bn_name' => 'প্রথম বিভাগ',
    ]);
    $secondDivision = Division::query()->create([
        'country_id' => 1,
        'name' => 'Public Second Division',
        'bn_name' => 'দ্বিতীয় বিভাগ',
    ]);
    $firstDistrict = District::query()->create([
        'division_id' => $firstDivision->id,
        'name' => 'Public First District',
        'bn_name' => 'প্রথম জেলা',
    ]);
    $secondDistrict = District::query()->create([
        'division_id' => $secondDivision->id,
        'name' => 'Public Second District',
        'bn_name' => 'দ্বিতীয় জেলা',
    ]);

    College::query()->create([
        'name' => 'Government District College',
        'college_type' => 'government',
        'division_id' => $firstDivision->id,
        'district_id' => $firstDistrict->id,
    ]);
    College::query()->create([
        'name' => 'Private District College',
        'college_type' => 'non_government',
        'division_id' => $secondDivision->id,
        'district_id' => $secondDistrict->id,
    ]);

    Livewire::test(AffiliatedCollegeDirectory::class)
        ->assertSeeHtml('data-public-district-filter')
        ->assertSeeHtml('disabled')
        ->assertDontSee('প্রথম জেলা')
        ->set('division', (string) $firstDivision->id)
        ->assertSet('district', '')
        ->assertSee('প্রথম জেলা')
        ->assertDontSee('দ্বিতীয় জেলা')
        ->set('district', (string) $firstDistrict->id)
        ->assertSee('Government District College')
        ->assertDontSee('Private District College')
        ->set('collegeType', 'non_government')
        ->assertDontSee('Government District College')
        ->set('division', (string) $secondDivision->id)
        ->assertSet('district', '')
        ->assertSee('Private District College');
});

it('renders an empty state when no public training is available', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('এই মুহূর্তে কোনো আসন্ন প্রশিক্ষণ নেই')
        ->assertSee('নতুন কোনো নোটিশ প্রকাশিত হয়নি');
});

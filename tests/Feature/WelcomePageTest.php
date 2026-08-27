<?php

use App\Enums\ApprovalStatus;
use App\Livewire\Frontend\AffiliatedCollegeDirectory;
use App\Models\College;
use App\Models\District;
use App\Models\Division;
use App\Models\ProgramLevel;
use App\Models\SystemSetting;
use App\Models\Teacher;
use App\Models\Training;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
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
        ->assertSee('AI Assistant')
        ->assertSee('Ask about this website...')
        ->assertSee('sessionStorage', false)
        ->assertSee('Dynamic Teacher Training')
        ->assertSee('১')
        ->assertSee('নিবন্ধিত শিক্ষক')
        ->assertSee('প্রশিক্ষণ কার্যক্রম')
        ->assertSee('অধিভুক্ত কলেজ')
        ->assertSee('৫০ জন');
});

it('applies saved frontend theme colors and default mode', function () {
    foreach ([
        SystemSetting::THEME_MODE => 'dark',
        SystemSetting::THEME_PRIMARY_LIGHT => '#1d4ed8',
        SystemSetting::THEME_PRIMARY_DARK => '#93c5fd',
        SystemSetting::THEME_ACCENT_LIGHT => '#7e22ce',
        SystemSetting::THEME_ACCENT_DARK => '#d8b4fe',
    ] as $key => $value) {
        SystemSetting::query()->create(compact('key', 'value'));
    }

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('--theme-primary-light: #1d4ed8', false)
        ->assertSee('--theme-primary-dark: #93c5fd', false)
        ->assertSee("localStorage.getItem('color-theme') || \"dark\"", false)
        ->assertSee('theme-hero-bg', false);
});

it('renders the frontend layout with fallback theme values', function () {
    $renderedLayout = Blade::render('<x-layouts::app.welcome><p>Public content</p></x-layouts::app.welcome>');

    expect($renderedLayout)
        ->toContain('--theme-primary-light: #047857')
        ->toContain("localStorage.getItem('color-theme') || \"system\"")
        ->toContain('Public content');
});

it('shows an accessible light and dark mode toggle on the frontend', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-theme-toggle', false)
        ->assertSee("this.$flux.appearance = this.$flux.dark ? 'light' : 'dark'", false)
        ->assertSee("'flux.appearance'", false)
        ->assertSee("localStorage.removeItem('color-theme')", false)
        ->assertSee('ডার্ক মোড চালু করুন')
        ->assertSee('লাইট মোড চালু করুন');
});

it('counts registrations with a qualified aggregate query and excludes draft trainings', function () {
    $publishedTraining = Training::factory()->create(['status' => 'Upcoming']);
    $draftTraining = Training::factory()->create(['status' => 'Draft']);
    $publishedTraining->participants()->attach(User::factory()->create());
    $draftTraining->participants()->attach(User::factory()->create());

    $response = $this->get(route('home'));

    $response->assertOk();

    expect($response->viewData('statistics')['registrations'])->toBe(1);
});

it('reuses public visibility and publication query constraints', function () {
    $approvedCollege = College::query()->create([
        'name' => 'Visible College',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    College::query()->create([
        'name' => 'Inactive College',
        'is_active' => false,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    College::query()->create([
        'name' => 'Pending College',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Pending,
    ]);
    Training::factory()->create(['status' => 'Upcoming']);
    Training::factory()->create(['status' => 'Draft']);

    expect(College::query()->publiclyVisible()->pluck('id')->all())->toBe([$approvedCollege->id])
        ->and($approvedCollege->isPubliclyVisible())->toBeTrue()
        ->and(Training::query()->published()->count())->toBe(1);
});

it('lets public users browse affiliated colleges and their subjects', function () {
    ProgramLevel::query()->updateOrCreate(
        ['slug' => 'postgraduate'],
        ['name' => 'স্নাতকোত্তর', 'sort_order' => 60, 'is_active' => true],
    );

    $college = College::query()->create([
        'college_code' => '2020',
        'name' => 'Public Affiliated College',
        'college_email' => 'college@example.com',
        'college_type' => 'government',
        'male_female' => 'B',
        'total_land' => '3.5 acres',
        'about' => str_repeat('কলেজ সম্পর্কে বিস্তারিত পরিচিতি। ', 12),
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $college->programs()->create([
        'level' => 'postgraduate',
        'name' => 'Honours',
        'items' => ['বাংলা', 'ইতিহাস'],
    ]);
    User::factory()->withRole('principal')->create([
        'name' => 'Role Holder Name',
        'college_id' => $college->id,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Public Affiliated College')
        ->assertSee('বাংলা')
        ->assertSee('প্রধান নেভিগেশন')
        ->assertSee($college->publicProfileUrl());

    $this->get(route('public.colleges.index'))
        ->assertOk()
        ->assertSeeHtml('data-affiliated-colleges-table')
        ->assertSeeInOrder(['কলেজ কোড', 'কলেজের নাম', 'ইমেইল', 'কলেজের ধরন', 'অ্যাকশন'])
        ->assertSee('2020')
        ->assertSee('Public Affiliated College')
        ->assertSee('college@example.com')
        ->assertSee('সরকারি')
        ->assertSee('দেখুন')
        ->assertSee('প্রধান নেভিগেশন')
        ->assertSee('অধিভুক্ত কলেজ ডিরেক্টরি');

    Livewire::test(AffiliatedCollegeDirectory::class)
        ->call('viewCollege', $college->id)
        ->assertSet('selectedCollegeId', $college->id)
        ->assertSet('showCollegeModal', true)
        ->assertSeeHtml('data-affiliated-college-modal')
        ->assertSee('কলেজের তথ্য')
        ->assertSee('শিক্ষার্থীভিত্তিক ধরন')
        ->assertSee('বয়েজ এন্ড গার্লস মিশ্র কলেজ')
        ->assertSee('মোট জমি')
        ->assertSee('3.5 acres')
        ->assertSee('ঠিকানা ও অবস্থান')
        ->assertSee('অধিভুক্ত বিষয় ও কোর্সসমূহ')
        ->assertSeeHtml('data-expand-college-about')
        ->assertSeeHtml("'line-clamp-3': ! expanded")
        ->assertSee('আরও দেখুন')
        ->assertSee('কম দেখুন')
        ->assertSee('college@example.com')
        ->assertSee('বাংলা')
        ->assertSeeHtml('data-college-profile-link')
        ->assertSee($college->publicProfileUrl())
        ->call('closeCollegeModal')
        ->assertSet('selectedCollegeId', null)
        ->assertSet('showCollegeModal', false)
        ->set('search', 'ইতিহাস')
        ->assertSee('Public Affiliated College')
        ->set('search', 'Not available')
        ->assertDontSee('Public Affiliated College');

    $this->get($college->publicProfileUrl())
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.$college->publicProfileUrl().'" />', false)
        ->assertSee('Role Holder Name')
        ->assertSee('স্নাতকোত্তর')
        ->assertSee('বাংলা')
        ->assertSee('প্রধান নেভিগেশন')
        ->assertSee('ইতিহাস');
});

it('shows the unassigned principal message on a public college profile', function () {
    $college = College::query()->create([
        'name' => 'Public College Without Principal',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    $this->get($college->publicProfileUrl())
        ->assertOk()
        ->assertSee('এখনো রুলস এসাইন করা হয়নি');
});

it('uses one canonical SEO-friendly URL for a public college profile', function () {
    $college = College::query()->create([
        'name' => 'SEO Friendly Public College',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    expect($college->publicProfileUrl())->toEndWith('/affiliated-colleges/'.$college->id.'/seo-friendly-public-college');

    $this->get(route('public.colleges.legacy-show', $college))
        ->assertMovedPermanently()
        ->assertRedirect($college->publicProfileUrl());

    $this->get(route('public.colleges.show', ['college' => $college, 'slug' => 'incorrect-name']))
        ->assertNotFound();
});

it('suggests other approved colleges from the same region on a college profile', function () {
    $division = Division::query()->create([
        'country_id' => 1,
        'name' => 'Suggestion Division',
        'bn_name' => 'সাজেশন বিভাগ',
    ]);
    $district = District::query()->create([
        'division_id' => $division->id,
        'name' => 'Suggestion District',
        'bn_name' => 'সাজেশন জেলা',
    ]);
    $otherDistrict = District::query()->create([
        'division_id' => $division->id,
        'name' => 'Nearby District',
        'bn_name' => 'নিকটবর্তী জেলা',
    ]);
    $otherDivision = Division::query()->create([
        'country_id' => 1,
        'name' => 'Unrelated Division',
        'bn_name' => 'অন্য বিভাগ',
    ]);

    $college = College::query()->create([
        'name' => 'Visited Regional College',
        'division_id' => $division->id,
        'district_id' => $district->id,
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $sameDistrictCollege = College::query()->create([
        'name' => 'Same District College',
        'division_id' => $division->id,
        'district_id' => $district->id,
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $sameDivisionCollege = College::query()->create([
        'name' => 'Same Division College',
        'division_id' => $division->id,
        'district_id' => $otherDistrict->id,
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $additionalColleges = collect(range(1, 3))->map(fn (int $number): College => College::query()->create([
        'name' => "Regional Carousel College {$number}",
        'division_id' => $division->id,
        'district_id' => $otherDistrict->id,
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]));
    College::query()->create([
        'name' => 'Unrelated Regional College',
        'division_id' => $otherDivision->id,
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);

    $this->get($college->publicProfileUrl())
        ->assertOk()
        ->assertSeeHtml('data-related-colleges')
        ->assertSeeHtml('data-related-colleges-carousel')
        ->assertSeeHtml('data-carousel-previous')
        ->assertSeeHtml('data-carousel-next')
        ->assertSeeHtml('data-carousel-viewport')
        ->assertSeeInOrder(['Same District College', 'Same Division College'])
        ->assertSee($sameDistrictCollege->publicProfileUrl())
        ->assertSee($sameDivisionCollege->publicProfileUrl())
        ->assertSee($additionalColleges->last()->publicProfileUrl())
        ->assertDontSee('Unrelated Regional College');
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

    $this->get($college->publicProfileUrl())->assertNotFound();
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

    foreach (range(1, 13) as $collegeNumber) {
        College::query()->create([
            'name' => "Government Paginated College {$collegeNumber}",
            'college_type' => 'government',
            'division_id' => $firstDivision->id,
            'district_id' => $firstDistrict->id,
        ]);
    }

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
        ->call('setPage', 2)
        ->assertSet('paginators.page', 2)
        ->assertDontSee('?page=', false)
        ->set('collegeType', 'non_government')
        ->assertSet('collegeType', 'non_government')
        ->assertSet('paginators.page', 1)
        ->assertDontSee('collegeType=', false)
        ->assertDontSee('?page=', false)
        ->assertDontSee('Government District College')
        ->set('division', (string) $secondDivision->id)
        ->assertSet('district', '')
        ->assertSee('Private District College');

    Livewire::withQueryParams([
        'collegeType' => 'government',
        'page' => 2,
    ])->test(AffiliatedCollegeDirectory::class)
        ->assertSet('collegeType', '')
        ->assertSet('paginators.page', 1);
});

it('renders an empty state when no public training is available', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('এই মুহূর্তে কোনো আসন্ন প্রশিক্ষণ নেই')
        ->assertSee('নতুন কোনো নোটিশ প্রকাশিত হয়নি');
});

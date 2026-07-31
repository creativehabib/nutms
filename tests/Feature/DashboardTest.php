<?php

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\College;
use App\Models\SystemSetting;
use App\Models\Teacher;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard shows college lab and ICT training report totals', function () {
    $user = User::factory()->create();

    Teacher::query()->create([
        'name' => 'Trained Teacher',
        'college_code' => '1001',
        'has_computer_lab' => 'yes',
        'computer_count' => 25,
        'ict_training_name' => 'Digital Content Creation',
    ]);
    Teacher::query()->create([
        'name' => 'Second Teacher In Same College',
        'college_code' => '1001',
        'has_computer_lab' => 'no',
        'ict_training_name' => null,
    ]);
    Teacher::query()->create([
        'name' => 'Teacher Without Training',
        'college_code' => '1002',
        'has_computer_lab' => 'no',
        'ict_training_name' => '',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertViewHas('report', [
            'collegesWithLab' => 1,
            'collegesWithoutLab' => 1,
            'totalColleges' => 2,
            'totalComputers' => 25,
            'labCoverage' => 50.0,
            'teachersWithIctTraining' => 1,
            'teachersWithoutIctTraining' => 2,
            'totalTeachers' => 3,
            'ictTrainingCoverage' => 33.3,
            'lastUpdatedAt' => Carbon::parse(Teacher::query()->max('updated_at'))->format('d M Y, h:i A'),
        ])
        ->assertSee('কম্পিউটার ল্যাব রিপোর্ট')
        ->assertSee('আইসিটি ট্রেনিং রিপোর্ট')
        ->assertSee('মোট কম্পিউটার')
        ->assertSee('আইসিটি ট্রেনিং কভারেজ');
});

test('principal dashboard links to their full profiles and college teachers', function () {
    $college = College::query()->create(['name' => 'Dashboard Principal College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create([
        'role' => UserRole::Principal,
        'college_id' => $college->id,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $profile = Teacher::query()->create([
        'user_id' => $principal->id,
        'college_id' => $college->id,
        'name' => 'Dashboard Principal',
        'present_address' => 'Principal Full Address',
        'approval_status' => ApprovalStatus::Approved,
    ]);

    $this->actingAs($principal)->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('প্রিন্সিপাল ড্যাশবোর্ড')
        ->assertSee('আমার প্রোফাইল')
        ->assertSee(route('teachers.show', $profile), false)
        ->assertSee(route('colleges.show', $college), false)
        ->assertSee(route('teachers.manage'), false)
        ->assertDontSee('কম্পিউটার ল্যাব রিপোর্ট')
        ->assertDontSee('আইসিটি ট্রেনিং রিপোর্ট');

    $this->actingAs($principal)->get(route('teachers.show', $profile))
        ->assertSuccessful()
        ->assertSee('Principal Full Address')
        ->assertSee('সম্পাদনা');
});

test('principal dashboard summarizes subjects trainings and retirement dates', function () {
    SystemSetting::query()->updateOrCreate(['key' => SystemSetting::RETIREMENT_AGE], ['value' => '59']);
    $college = College::query()->create(['name' => 'Analytics College', 'approval_status' => ApprovalStatus::Approved]);
    $principal = User::factory()->create(['role' => UserRole::Principal, 'college_id' => $college->id, 'approval_status' => ApprovalStatus::Approved]);
    $retired = Teacher::query()->create(['college_id' => $college->id, 'name' => 'Retired Teacher', 'subject' => 'Physics', 'birth_date' => now()->subYears(60), 'approval_status' => ApprovalStatus::Approved]);
    $upcoming = Teacher::query()->create(['college_id' => $college->id, 'name' => 'Upcoming Teacher', 'subject' => 'Physics', 'birth_date' => now()->subYears(58)->subMonths(6), 'approval_status' => ApprovalStatus::Approved]);
    Teacher::query()->create(['college_id' => $college->id, 'name' => 'No Birth Date Teacher', 'subject' => 'Chemistry', 'approval_status' => ApprovalStatus::Approved]);
    $institute = TrainingInstitute::query()->create(['name' => 'Dashboard Institute']);
    $training = TrainingType::query()->create(['training_institute_id' => $institute->id, 'name' => 'Digital Content', 'duration_value' => 5, 'duration_unit' => 'days']);
    $retired->trainingTypes()->attach($training->id, ['training_year' => now()->year]);
    $upcoming->trainingTypes()->attach($training->id, ['training_year' => now()->year]);

    $this->actingAs($principal)->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Physics')->assertSee('2 জন')
        ->assertSee('Chemistry')
        ->assertSee('Digital Content')
        ->assertSee('অবসরপ্রাপ্ত 1 জন')
        ->assertSee('আগামী ১ বছরে 1 জন')
        ->assertSee('Retired Teacher')
        ->assertSee('Upcoming Teacher')
        ->assertSee('1 জন শিক্ষকের জন্ম তারিখ যোগ করা হয়নি।');
});

test('teacher dashboard shows only their profile workflow', function () {
    $teacher = User::factory()->create(['role' => UserRole::Teacher]);

    $this->actingAs($teacher)->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('শিক্ষক ড্যাশবোর্ড')
        ->assertSee('শিক্ষক প্রোফাইল')
        ->assertDontSee('কম্পিউটার ল্যাব রিপোর্ট')
        ->assertDontSee('আইসিটি ট্রেনিং রিপোর্ট');
});

test('sidebar menu items use icons that match their destinations', function () {
    $sidebar = file_get_contents(resource_path('views/layouts/app/sidebar.blade.php'));

    expect($sidebar)
        ->toContain('icon="layout-grid" :href="route(\'dashboard\')"')
        ->toContain('icon="user-group" :href="route(\'teachers.manage\')"')
        ->toContain('icon="computer-desktop" :href="route(\'lab.summary\')"')
        ->toContain('icon="academic-cap" :href="route(\'ict.summary\')"');
});

test('custom application screens include dark mode surfaces and text', function () {
    $views = [
        'livewire/teacher-management.blade.php',
        'livewire/teacher-data-import.blade.php',
        'livewire/college-lab-summary.blade.php',
        'livewire/ict-training-summary.blade.php',
    ];

    foreach ($views as $view) {
        $contents = file_get_contents(resource_path("views/{$view}"));

        expect($contents)
            ->toContain('dark:bg-')
            ->toContain('dark:text-')
            ->toContain('dark:border-');
    }
});

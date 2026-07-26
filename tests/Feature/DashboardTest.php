<?php

use App\Models\Teacher;
use App\Models\User;

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
            'teachersWithIctTraining' => 1,
            'teachersWithoutIctTraining' => 2,
            'totalTeachers' => 3,
        ])
        ->assertSee('কম্পিউটার ল্যাব রিপোর্ট')
        ->assertSee('আইসিটি ট্রেনিং রিপোর্ট');
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

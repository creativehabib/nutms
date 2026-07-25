<?php

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

test('sidebar menu items use icons that match their destinations', function () {
    $sidebar = file_get_contents(resource_path('views/layouts/app/sidebar.blade.php'));

    expect($sidebar)
        ->toContain('icon="layout-grid" :href="route(\'dashboard\')"')
        ->toContain('icon="user-group" :href="route(\'teachers.manage\')"')
        ->toContain('icon="computer-desktop" :href="route(\'lab.summary\')"')
        ->toContain('icon="academic-cap" :href="route(\'ict.summary\')"');
});

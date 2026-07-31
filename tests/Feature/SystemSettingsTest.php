<?php

use App\Enums\UserRole;
use App\Livewire\SystemSettings;
use App\Models\SystemSetting;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to configure the teacher retirement age', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    Livewire::actingAs($admin)->test(SystemSettings::class)
        ->set('retirementAge', 60)
        ->call('save')
        ->assertHasNoErrors();

    expect(SystemSetting::retirementAge())->toBe(60);
});

it('does not allow a principal to configure the retirement age', function () {
    $principal = User::factory()->create(['role' => UserRole::Principal]);

    $this->actingAs($principal)->get(route('system-settings.manage'))->assertForbidden();
});

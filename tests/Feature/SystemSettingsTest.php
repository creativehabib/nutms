<?php

use App\Livewire\SystemSettings;
use App\Models\SystemSetting;
use App\Models\User;
use Livewire\Livewire;

it('allows an admin to configure the teacher retirement age', function () {
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(SystemSettings::class)
        ->set('retirementAge', 60)
        ->call('save')
        ->assertHasNoErrors();

    expect(SystemSetting::retirementAge())->toBe(60);
});

it('does not allow a principal to configure the retirement age', function () {
    $principal = User::factory()->withRole('principal')->create();

    $this->actingAs($principal)->get(route('system-settings.manage'))->assertForbidden();
});

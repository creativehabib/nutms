<?php

use App\Livewire\SystemSettings;
use App\Models\AiSetting;
use App\Models\EmailSetting;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\EnvironmentFileUpdater;
use Livewire\Livewire;

use function Pest\Laravel\mock;

it('allows an admin to configure the teacher retirement age', function () {
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(SystemSettings::class)
        ->set('retirementAge', 60)
        ->call('save')
        ->assertHasNoErrors();

    expect(SystemSetting::retirementAge())->toBe(60);
});

it('stores encrypted SMTP settings and synchronizes the environment file', function () {
    $admin = User::factory()->withRole('admin')->create();
    mock(EnvironmentFileUpdater::class)
        ->shouldReceive('update')
        ->once()
        ->withArgs(fn (array $values): bool => $values['MAIL_HOST'] === 'smtp.example.com'
            && $values['MAIL_PASSWORD'] === 'smtp-secret'
            && $values['APPROVAL_EMAILS_ENABLED'] === true);

    Livewire::actingAs($admin)->test(SystemSettings::class)
        ->set('emailEnabled', true)
        ->set('mailHost', 'smtp.example.com')
        ->set('mailPort', 587)
        ->set('mailScheme', 'smtp')
        ->set('mailUsername', 'mailer@example.com')
        ->set('mailPassword', 'smtp-secret')
        ->set('mailFromAddress', 'training@example.com')
        ->set('mailFromName', 'NU Training')
        ->call('saveEmailSettings')
        ->assertHasNoErrors()
        ->assertSet('mailPassword', '');

    $settings = EmailSetting::query()->firstOrFail();
    expect($settings->password)->toBe('smtp-secret')
        ->and($settings->is_enabled)->toBeTrue()
        ->and(config('mail.default'))->toBe('smtp')
        ->and(config('mail.approval_notifications_enabled'))->toBeTrue()
        ->and(config('mail.from.address'))->toBe('training@example.com');
});

it('does not allow a principal to configure the retirement age', function () {
    $principal = User::factory()->withRole('principal')->create();

    $this->actingAs($principal)->get(route('system-settings.manage'))->assertForbidden();
});

it('stores an encrypted AI provider configuration', function () {
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(SystemSettings::class)
        ->set('aiEnabled', true)
        ->set('aiProvider', 'openai')
        ->set('aiModel', 'gpt-4o-mini')
        ->set('aiEndpoint', 'https://api.openai.com/v1/')
        ->set('aiApiKey', 'secret-ai-key')
        ->set('aiHistoryLimit', 8)
        ->call('saveAiSettings')
        ->assertHasNoErrors()
        ->assertSet('aiApiKey', '');

    $setting = AiSetting::query()->firstOrFail();
    expect($setting->is_enabled)->toBeTrue()
        ->and($setting->api_key)->toBe('secret-ai-key')
        ->and($setting->endpoint)->toBe('https://api.openai.com/v1')
        ->and($setting->history_limit)->toBe(8);
});

it('shows AI setup guidance and official provider links to an admin', function () {
    $admin = User::factory()->withRole('admin')->create();

    Livewire::actingAs($admin)->test(SystemSettings::class)
        ->assertSee('AI Setting Helper')
        ->assertSee('Create OpenAI API Key')
        ->assertSeeHtml('https://platform.openai.com/api-keys')
        ->assertSeeHtml('https://platform.openai.com/docs/models')
        ->assertSeeHtml('rel="noopener noreferrer"');
});

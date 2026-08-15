<?php

namespace App\Livewire;

use App\Models\AiSetting;
use App\Models\EmailSetting;
use App\Models\SystemSetting;
use App\Services\AiChatService;
use App\Services\EnvironmentFileUpdater;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;
use RuntimeException;

class SystemSettings extends Component
{
    public int $retirementAge = 59;
    public bool $emailEnabled = false;
    public string $mailHost = '127.0.0.1';
    public int $mailPort = 587;
    public string $mailScheme = '';
    public string $mailUsername = '';
    public string $mailPassword = '';
    public string $mailFromAddress = '';
    public string $mailFromName = '';
    public bool $aiEnabled = false;
    public string $aiProvider = 'openai';
    public string $aiModel = 'gpt-4o-mini';
    public string $aiEndpoint = 'https://api.openai.com/v1';
    public string $aiApiKey = '';
    public string $aiSystemPrompt = '';
    public int $aiHistoryLimit = 10;
    public bool $aiHasApiKey = false;
    public ?string $aiConnectionMessage = null;
    public ?bool $aiConnectionSuccessful = null;
    public string $savedAiProvider = 'openai';
    public int $aiRetentionDays = 30;
    public bool $aiSaveGuestConversations = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $this->retirementAge = SystemSetting::retirementAge();
        $emailSetting = EmailSetting::query()->latest('id')->first();

        if ($emailSetting !== null) {
            $this->emailEnabled = $emailSetting->is_enabled;
            $this->mailHost = $emailSetting->host;
            $this->mailPort = $emailSetting->port;
            $this->mailScheme = $emailSetting->scheme ?? '';
            $this->mailUsername = $emailSetting->username ?? '';
            $this->mailFromAddress = $emailSetting->from_address;
            $this->mailFromName = $emailSetting->from_name;
        } else {
            $this->mailFromAddress = (string) config('mail.from.address');
            $this->mailFromName = (string) config('mail.from.name');
        }

        $aiSetting = AiSetting::query()->latest('id')->first();
        if ($aiSetting !== null) {
            $this->aiEnabled = $aiSetting->is_enabled;
            $this->aiProvider = $aiSetting->provider;
            $this->savedAiProvider = $aiSetting->provider;
            $this->aiModel = $aiSetting->model;
            $this->aiEndpoint = $aiSetting->provider === 'gemini'
                ? (string) preg_replace('#/openai/?$#', '', $aiSetting->endpoint)
                : $aiSetting->endpoint;
            $this->aiSystemPrompt = $aiSetting->system_prompt ?? '';
            $this->aiHistoryLimit = $aiSetting->history_limit;
            $this->aiHasApiKey = filled($aiSetting->api_key);
            $this->aiRetentionDays = $aiSetting->retention_days;
            $this->aiSaveGuestConversations = $aiSetting->save_guest_conversations;
        }
    }

    public function saveAiSettings(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $validated = $this->validate([
            'aiEnabled' => ['boolean'],
            'aiProvider' => ['required', Rule::in(['openai', 'gemini', 'groq', 'openrouter', 'compatible'])],
            'aiModel' => ['required', 'string', 'max:100'],
            'aiEndpoint' => ['required', 'url:http,https', 'max:255'],
            'aiApiKey' => ['nullable', 'string', 'max:1000'],
            'aiSystemPrompt' => ['nullable', 'string', 'max:5000'],
            'aiHistoryLimit' => ['required', 'integer', 'min:2', 'max:30'],
            'aiRetentionDays' => ['required', 'integer', 'min:1', 'max:365'],
            'aiSaveGuestConversations' => ['boolean'],
        ]);

        if ($validated['aiProvider'] !== $this->savedAiProvider && blank($validated['aiApiKey'])) {
            $this->addError('aiApiKey', __('Enter an API key for the newly selected provider.'));

            return;
        }

        $setting = AiSetting::query()->latest('id')->firstOrNew();
        $setting->fill([
            'is_enabled' => $validated['aiEnabled'],
            'provider' => $validated['aiProvider'],
            'model' => $validated['aiModel'],
            'endpoint' => rtrim($validated['aiEndpoint'], '/'),
            'system_prompt' => $validated['aiSystemPrompt'] ?: null,
            'history_limit' => $validated['aiHistoryLimit'],
            'retention_days' => $validated['aiRetentionDays'],
            'save_guest_conversations' => $validated['aiSaveGuestConversations'],
        ]);
        if (filled($validated['aiApiKey'])) {
            $setting->api_key = $validated['aiApiKey'];
        }
        $setting->save();
        $this->savedAiProvider = $setting->provider;
        $this->aiHasApiKey = filled($setting->api_key);
        $this->aiConnectionMessage = null;
        $this->aiConnectionSuccessful = null;
        $this->reset('aiApiKey');
        Flux::toast(variant: 'success', text: __('AI settings have been saved.'));
    }

    public function updatedAiProvider(string $provider): void
    {
        $preset = match ($provider) {
            'openai' => ['https://api.openai.com/v1', 'gpt-4o-mini'],
            'gemini' => ['https://generativelanguage.googleapis.com/v1beta', 'gemini-2.5-flash'],
            'groq' => ['https://api.groq.com/openai/v1', 'llama-3.3-70b-versatile'],
            'openrouter' => ['https://openrouter.ai/api/v1', 'openrouter/free'],
            default => [$this->aiEndpoint, $this->aiModel],
        };

        [$this->aiEndpoint, $this->aiModel] = $preset;
        $this->aiConnectionMessage = null;
        $this->aiConnectionSuccessful = null;
        $this->resetErrorBag('aiApiKey');
    }

    public function testAiConnection(AiChatService $chatService): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $setting = AiSetting::query()->latest('id')->first();

        if ($setting === null || ! $setting->is_enabled) {
            $this->aiConnectionSuccessful = false;
            $this->aiConnectionMessage = __('Save the settings and enable the AI assistant before testing.');

            return;
        }

        try {
            $chatService->reply($setting, [['role' => 'user', 'content' => 'Reply only with OK.']]);
            $this->aiConnectionSuccessful = true;
            $this->aiConnectionMessage = __('Connection successful. The API key, endpoint, and model are working.');
        } catch (RuntimeException $exception) {
            $this->aiConnectionSuccessful = false;
            $this->aiConnectionMessage = $exception->getMessage();
        }
    }

    public function saveEmailSettings(EnvironmentFileUpdater $environmentFileUpdater): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $validated = $this->validate([
            'emailEnabled' => ['boolean'],
            'mailHost' => ['required', 'string', 'max:255'],
            'mailPort' => ['required', 'integer', 'min:1', 'max:65535'],
            'mailScheme' => ['nullable', Rule::in(['', 'smtp', 'smtps'])],
            'mailUsername' => ['nullable', 'string', 'max:255'],
            'mailPassword' => ['nullable', 'string', 'max:1000'],
            'mailFromAddress' => ['required', 'email:rfc', 'max:255'],
            'mailFromName' => ['required', 'string', 'max:255'],
        ]);

        $emailSetting = EmailSetting::query()->latest('id')->firstOrNew();
        $emailSetting->fill([
            'is_enabled' => $validated['emailEnabled'],
            'mailer' => 'smtp',
            'host' => $validated['mailHost'],
            'port' => $validated['mailPort'],
            'scheme' => $validated['mailScheme'] ?: null,
            'username' => $validated['mailUsername'] ?: null,
            'from_address' => $validated['mailFromAddress'],
            'from_name' => $validated['mailFromName'],
        ]);

        if (filled($validated['mailPassword'])) {
            $emailSetting->password = $validated['mailPassword'];
        }

        $emailSetting->save();
        config($emailSetting->mailConfiguration());
        Mail::purge('smtp');

        $environmentFileUpdater->update([
            'TRAINING_EMAILS_ENABLED' => $emailSetting->is_enabled,
            'APPROVAL_EMAILS_ENABLED' => $emailSetting->is_enabled,
            'MAIL_MAILER' => $emailSetting->is_enabled ? 'smtp' : 'log',
            'MAIL_SCHEME' => $emailSetting->scheme,
            'MAIL_HOST' => $emailSetting->host,
            'MAIL_PORT' => $emailSetting->port,
            'MAIL_USERNAME' => $emailSetting->username,
            'MAIL_PASSWORD' => $emailSetting->password,
            'MAIL_FROM_ADDRESS' => $emailSetting->from_address,
            'MAIL_FROM_NAME' => $emailSetting->from_name,
        ]);

        $this->reset('mailPassword');
        Flux::toast(variant: 'success', text: __('Email settings have been saved.'));
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $validated = $this->validate(['retirementAge' => ['required', 'integer', 'min:50', 'max:70']]);

        SystemSetting::query()->updateOrCreate(
            ['key' => SystemSetting::RETIREMENT_AGE],
            ['value' => (string) $validated['retirementAge']],
        );

        Flux::toast(variant: 'success', text: 'শিক্ষকদের অবসর বয়স সংরক্ষণ করা হয়েছে।');
    }

    public function render(): View
    {
        return view('livewire.system-settings')->layout('layouts.app', ['title' => 'System Settings']);
    }
}

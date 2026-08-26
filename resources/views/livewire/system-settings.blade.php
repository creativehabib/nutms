<div class="mx-auto w-full max-w-5xl space-y-6">

    <!-- ======================================= -->
    <!-- PAGE HEADER                             -->
    <!-- ======================================= -->
    <div class="flex flex-col gap-1.5 border-b border-zinc-200 pb-5 dark:border-zinc-800">
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">{{ __('System Settings') }}</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Manage global application configurations and email services.') }}</flux:text>
    </div>

    <form wire:submit="saveThemeSettings">
        <flux:card class="flex flex-col gap-6 border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800/80 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-600 dark:border-emerald-500/10 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <flux:icon.swatch class="size-6" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('Frontend Theme') }}</flux:heading>
                        <flux:text class="mt-0.5 text-sm">{{ __('Choose balanced brand colors for light and dark mode.') }}</flux:text>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-xl border border-zinc-200 bg-zinc-50 p-2 dark:border-zinc-700 dark:bg-zinc-800">
                    <span class="size-8 rounded-lg border border-black/10" style="background: {{ $themePrimaryLight }}"></span>
                    <span class="size-8 rounded-lg border border-white/10" style="background: {{ $themePrimaryDark }}"></span>
                    <span class="size-8 rounded-lg border border-black/10" style="background: {{ $themeAccentLight }}"></span>
                    <span class="size-8 rounded-lg border border-white/10" style="background: {{ $themeAccentDark }}"></span>
                </div>
            </div>

            <flux:select wire:model="themeMode" :label="__('Default color mode')" :description="__('Visitors can still use their saved browser preference.')">
                <option value="system">{{ __('Follow device setting') }}</option>
                <option value="light">{{ __('Light') }}</option>
                <option value="dark">{{ __('Dark') }}</option>
            </flux:select>

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model.live="themePrimaryLight" type="color" :label="__('Primary color — light mode')" />
                <flux:input wire:model.live="themePrimaryDark" type="color" :label="__('Primary color — dark mode')" />
                <flux:input wire:model.live="themeAccentLight" type="color" :label="__('Accent color — light mode')" />
                <flux:input wire:model.live="themeAccentDark" type="color" :label="__('Accent color — dark mode')" />
            </div>

            <flux:callout variant="info" icon="information-circle">
                {{ __('Primary colors are used for navigation and actions; accent colors highlight important frontend content.') }}
            </flux:callout>

            <div class="flex justify-end border-t border-zinc-100 pt-5 dark:border-zinc-800">
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveThemeSettings">{{ __('Save Theme') }}</span>
                    <span wire:loading wire:target="saveThemeSettings">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </flux:card>
    </form>

    <!-- ======================================= -->
    <!-- RETIREMENT SETTINGS CARD                -->
    <!-- ======================================= -->
    <form wire:submit="save">
        <flux:card class="flex flex-col gap-6 border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <!-- Card Header -->
            <div class="flex flex-col gap-4 border-b border-zinc-100 pb-4 dark:border-zinc-800/80 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shadow-sm border border-indigo-100 dark:border-indigo-500/10 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <flux:icon.clock class="size-6" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="text-zinc-900 dark:text-white">{{ __('Retirement Policy') }}</flux:heading>
                        <flux:text class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Configure teacher retirement rules used across dashboards and reports.') }}</flux:text>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <div class="max-w-md">
                <flux:input wire:model="retirementAge" type="number" min="50" max="70" :label="__('Retirement Age (Years)')" required />
            </div>

            <!-- Card Footer -->
            <div class="flex justify-end pt-2">
                <flux:button type="submit" variant="primary" icon="check" class="shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save Changes') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </flux:card>
    </form>

    <form wire:submit="saveAiSettings">
        <flux:card class="flex flex-col gap-6 border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800/80 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl border border-violet-100 bg-violet-50 text-violet-600 dark:border-violet-500/10 dark:bg-violet-500/10 dark:text-violet-400"><flux:icon.sparkles class="size-6" /></div>
                    <div><flux:heading size="lg">{{ __('AI Settings') }}</flux:heading><flux:text class="mt-0.5 text-sm">{{ __('Configure the website assistant for visitors and teachers.') }}</flux:text></div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <flux:modal.trigger name="ai-settings-helper">
                        <flux:button type="button" variant="ghost" icon="question-mark-circle">
                            {{ __('AI Setting Helper') }}
                        </flux:button>
                    </flux:modal.trigger>
                    <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 dark:border-zinc-700 dark:bg-zinc-800"><flux:switch wire:model="aiEnabled" :label="__('Enable AI assistant')" /></div>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:select wire:model.live="aiProvider" :label="__('Provider')">
                    <option value="openai">OpenAI</option>
                    <option value="gemini">Google Gemini</option>
                    <option value="groq">Groq</option>
                    <option value="openrouter">OpenRouter</option>
                    <option value="compatible">{{ __('Other OpenAI-compatible') }}</option>
                </flux:select>
                <flux:input wire:model="aiModel" :label="__('Model')" placeholder="gpt-4o-mini" required />
                <flux:input wire:model="aiEndpoint" type="url" :label="__('API Endpoint')" placeholder="https://api.openai.com/v1" required />
                <div>
                    <flux:input wire:model="aiApiKey" type="password" :label="__('API Key')" :description="$aiHasApiKey && $aiProvider === $savedAiProvider ? __('A saved API key is configured. Leave blank to keep it, or enter a new key to replace it.') : __('Enter an API key for the selected provider.')" autocomplete="new-password" />
                    <div class="mt-2">
                        <flux:badge :color="$aiHasApiKey && $aiProvider === $savedAiProvider ? 'green' : 'amber'" size="sm">
                            {{ $aiHasApiKey && $aiProvider === $savedAiProvider ? __('API key configured') : __('API key required for selected provider') }}
                        </flux:badge>
                    </div>
                    <flux:error name="aiApiKey" />
                </div>
                <flux:input wire:model="aiHistoryLimit" type="number" min="2" max="30" :label="__('Conversation history limit')" required />
                <div class="sm:col-span-2"><flux:textarea wire:model="aiSystemPrompt" rows="4" :label="__('Additional instructions')" :description="__('Optional organization-specific guidance. Security and website rules are always applied.')" /></div>
            </div>

            <flux:callout variant="warning" icon="shield-check">{{ __('The API key is encrypted. AI responses may be inaccurate; do not include sensitive personal information in prompts.') }}</flux:callout>

            @if($aiConnectionMessage !== null)
                <flux:callout :variant="$aiConnectionSuccessful ? 'success' : 'danger'" :icon="$aiConnectionSuccessful ? 'check-circle' : 'exclamation-triangle'">
                    {{ $aiConnectionMessage }}
                </flux:callout>
            @endif

            <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                <flux:button type="button" wire:click="testAiConnection" icon="signal" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="testAiConnection">{{ __('Test AI Connection') }}</span>
                    <span wire:loading wire:target="testAiConnection">{{ __('Testing connection...') }}</span>
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled"><span wire:loading.remove wire:target="saveAiSettings">{{ __('Save AI Settings') }}</span><span wire:loading wire:target="saveAiSettings">{{ __('Saving...') }}</span></flux:button>
            </div>
        </flux:card>
    </form>

    <flux:modal name="ai-settings-helper" class="max-w-2xl">
        <div class="space-y-6">
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <flux:icon.sparkles class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg">{{ __('AI Settings Help') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Follow these steps to connect the website assistant safely.') }}</flux:text>
                </div>
            </div>

            <ol class="space-y-4 text-sm text-zinc-700 dark:text-zinc-300">
                <li class="flex gap-3"><flux:badge color="violet">1</flux:badge><span><strong>{{ __('Create an API key:') }}</strong> {{ __('Sign in to your AI provider and create a secret API key. Never share this key with users.') }}</span></li>
                <li class="flex gap-3"><flux:badge color="violet">2</flux:badge><span><strong>{{ __('Select the provider:') }}</strong> {{ __('Choose OpenAI for the official API, or OpenAI-compatible for another service using the same chat completions format.') }}</span></li>
                <li class="flex gap-3"><flux:badge color="violet">3</flux:badge><span><strong>{{ __('Enter model and endpoint:') }}</strong> {{ __('For OpenAI, keep the endpoint as https://api.openai.com/v1 and enter a model available to your account.') }}</span></li>
                <li class="flex gap-3"><flux:badge color="violet">4</flux:badge><span><strong>{{ __('Test gradually:') }}</strong> {{ __('Save the settings, enable the assistant, and ask a simple website question. Monitor provider usage and costs regularly.') }}</span></li>
            </ol>

            <flux:callout variant="info" icon="information-circle">
                {{ __('Gemini, Groq, and OpenRouter may offer limited free usage. Free quotas, available models, and eligibility are controlled by each provider and can change. A provider API key is still required.') }}
            </flux:callout>

            <flux:callout variant="warning" icon="shield-check">
                {{ __('Do not place personal, confidential, or authentication information in Additional Instructions. The saved API key is encrypted and is never displayed again.') }}
            </flux:callout>

            <div class="flex flex-wrap gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                <flux:button href="https://platform.openai.com/api-keys" target="_blank" rel="noopener noreferrer" icon="key">
                    {{ __('Create OpenAI API Key') }}
                </flux:button>
                <flux:button href="https://platform.openai.com/docs/models" target="_blank" rel="noopener noreferrer" variant="ghost" icon="book-open">
                    {{ __('View OpenAI Models') }}
                </flux:button>
                <flux:button href="https://platform.openai.com/docs/guides/text-generation" target="_blank" rel="noopener noreferrer" variant="ghost" icon="arrow-top-right-on-square">
                    {{ __('API Documentation') }}
                </flux:button>
                <flux:button href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" variant="ghost" icon="key">
                    {{ __('Get Gemini API Key') }}
                </flux:button>
                <flux:button href="https://console.groq.com/keys" target="_blank" rel="noopener noreferrer" variant="ghost" icon="key">
                    {{ __('Get Groq API Key') }}
                </flux:button>
                <flux:button href="https://openrouter.ai/settings/keys" target="_blank" rel="noopener noreferrer" variant="ghost" icon="key">
                    {{ __('Get OpenRouter API Key') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- ======================================= -->
    <!-- EMAIL SETTINGS CARD                     -->
    <!-- ======================================= -->
    <form wire:submit="saveEmailSettings">
        <flux:card class="flex flex-col gap-6 border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <!-- Card Header -->
            <div class="flex flex-col gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800/80 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 shadow-sm border border-sky-100 dark:border-sky-500/10 dark:bg-sky-500/10 dark:text-sky-400">
                        <flux:icon.envelope class="size-6" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="text-zinc-900 dark:text-white">{{ __('Email Settings') }}</flux:heading>
                        <flux:text class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Send teachers their training selection and certificate status by email.') }}</flux:text>
                    </div>
                </div>

                <!-- Switch inside a highlighted box -->
                <div class="flex items-center rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-2.5 dark:border-zinc-700/50 dark:bg-zinc-800/50 shadow-sm">
                    <flux:switch wire:model="emailEnabled" :label="__('Enable training emails')" />
                </div>
            </div>

            <!-- Form Body -->
            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model="mailHost" :label="__('SMTP Host')" placeholder="smtp.example.com" required />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="mailPort" type="number" min="1" max="65535" :label="__('SMTP Port')" required />
                    <flux:select wire:model="mailScheme" :label="__('Security')">
                        <option value="">{{ __('Automatic') }}</option>
                        <option value="smtp">SMTP / STARTTLS</option>
                        <option value="smtps">SMTPS</option>
                    </flux:select>
                </div>

                <flux:input wire:model="mailUsername" :label="__('SMTP Username')" autocomplete="off" />

                <flux:input wire:model="mailPassword" type="password" :label="__('SMTP Password')" :description="__('Leave blank to keep the current password.')" autocomplete="new-password" />

                <flux:input wire:model="mailFromAddress" type="email" :label="__('From Email Address')" required />

                <flux:input wire:model="mailFromName" :label="__('From Name')" required />
            </div>

            <!-- Alert Callout -->
            <flux:callout variant="warning" icon="shield-check" class="mt-4 !text-amber-900 dark:!text-amber-200">
                {{ __('The SMTP password is encrypted in the database. Saving also synchronizes these values to the server .env file.') }}
            </flux:callout>

            <!-- Card Footer -->
            <div class="flex justify-end border-t border-zinc-100 pt-5 dark:border-zinc-800/80">
                <flux:button type="submit" variant="primary" icon="check" class="shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveEmailSettings">{{ __('Save Email Settings') }}</span>
                    <span wire:loading wire:target="saveEmailSettings">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>

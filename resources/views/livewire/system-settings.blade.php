<div class="mx-auto w-full max-w-5xl space-y-6">

    <!-- ======================================= -->
    <!-- PAGE HEADER                             -->
    <!-- ======================================= -->
    <div class="flex flex-col gap-1.5 border-b border-zinc-200 pb-5 dark:border-zinc-800">
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">{{ __('System Settings') }}</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Manage global application configurations and email services.') }}</flux:text>
    </div>

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

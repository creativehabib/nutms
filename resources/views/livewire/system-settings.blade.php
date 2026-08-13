<div class="mx-auto w-full max-w-4xl space-y-6 p-4 sm:p-6">
    <div>
        <flux:heading size="xl">{{ __('System Settings') }}</flux:heading>
        <flux:text>{{ __('Configure teacher retirement rules used across dashboards and reports.') }}</flux:text>
    </div>

    <form wire:submit="save">
        <flux:card class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Configure teacher retirement rules used across dashboards and reports.') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Configure teacher retirement rules used across dashboards and reports.') }}</flux:text>
            </div>
            <flux:input wire:model="retirementAge" type="number" min="50" max="70"  :label="__('Retirement Age')" required />
            <div class="flex justify-end"><flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button></div>
        </flux:card>
    </form>

    <form wire:submit="saveEmailSettings">
        <flux:card class="space-y-5">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <flux:heading size="lg">{{ __('Email Settings') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Send teachers their training selection and certificate status by email.') }}</flux:text>
                </div>
                <flux:switch wire:model="emailEnabled" :label="__('Enable training emails')" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="mailHost" :label="__('SMTP Host')" placeholder="smtp.example.com" required />
                <flux:input wire:model="mailPort" type="number" min="1" max="65535" :label="__('SMTP Port')" required />
                <flux:select wire:model="mailScheme" :label="__('Security')">
                    <option value="">{{ __('Automatic') }}</option>
                    <option value="smtp">SMTP / STARTTLS</option>
                    <option value="smtps">SMTPS</option>
                </flux:select>
                <flux:input wire:model="mailUsername" :label="__('SMTP Username')" autocomplete="off" />
                <flux:input wire:model="mailPassword" type="password" :label="__('SMTP Password')" :description="__('Leave blank to keep the current password.')" autocomplete="new-password" />
                <flux:input wire:model="mailFromAddress" type="email" :label="__('From Email Address')" required />
                <flux:input wire:model="mailFromName" :label="__('From Name')" required />
            </div>

            <flux:callout variant="warning" icon="shield-check">
                {{ __('The SMTP password is encrypted in the database. Saving also synchronizes these values to the server .env file.') }}
            </flux:callout>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Save Email Settings') }}</flux:button>
            </div>
        </flux:card>
    </form>
</div>

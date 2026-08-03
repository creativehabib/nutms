<div class="mx-auto w-full max-w-3xl space-y-6 p-4 sm:p-6">
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
</div>

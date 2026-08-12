<div class="mx-auto w-full max-w-7xl p-4 sm:p-6">
    <div class="mb-6">
        <flux:heading size="xl" class="font-bold">{{ __('My Training Certificates') }}</flux:heading>
        <flux:subheading>{{ __('Download certificates issued for your completed trainings.') }}</flux:subheading>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($trainings as $training)
            @php($registration = $training->participants->first()?->pivot)
            <flux:card wire:key="training-certificate-{{ $training->id }}" class="flex flex-col gap-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="rounded-xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-950 dark:text-amber-300"><flux:icon.trophy class="size-6" /></div>
                    <flux:badge color="green">{{ __('Completed') }}</flux:badge>
                </div>
                <div class="flex-1">
                    <flux:heading size="lg">{{ $training->title }}</flux:heading>
                    <flux:text class="mt-2 text-sm">{{ __('Completed') }}: {{ \Illuminate\Support\Carbon::parse($registration->completed_at)->format('d M Y') }}</flux:text>
                    <flux:text class="mt-1 text-xs">{{ __('Certificate No.') }}: {{ $registration->certificate_number }}</flux:text>
                </div>
                <flux:button variant="primary" icon="arrow-down-tray" :href="route('trainings.certificate', $training)">{{ __('Download Certificate') }}</flux:button>
            </flux:card>
        @empty
            <flux:card class="md:col-span-2 xl:col-span-3">
                <div class="flex flex-col items-center gap-3 py-10 text-center"><flux:icon.trophy class="size-10 text-zinc-300" /><flux:heading>{{ __('No certificates are available yet.') }}</flux:heading><flux:text>{{ __('Your certificates will appear here after an approved training is completed.') }}</flux:text></div>
            </flux:card>
        @endforelse
    </div>

    @if ($trainings->hasPages())<div class="mt-6">{{ $trainings->links() }}</div>@endif
</div>

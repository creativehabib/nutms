<div>
@if ($compact)
<flux:card class="overflow-hidden border-zinc-200 p-0 shadow-sm dark:border-zinc-700">
    <div class="flex flex-col gap-3 border-b border-zinc-200 bg-zinc-50/70 px-5 py-4 dark:border-zinc-700 dark:bg-zinc-900/50 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300">
                <flux:icon.academic-cap class="size-5" />
            </div>
            <div class="min-w-0">
                <flux:heading size="lg">{{ __('Upcoming Trainings') }}</flux:heading>
                <flux:text class="truncate text-sm">{{ __('New opportunities available for registration.') }}</flux:text>
            </div>
        </div>
        <flux:button :href="route('training.calendar')" wire:navigate variant="outline" size="sm" icon-trailing="arrow-right">
            {{ __('Calendar & all trainings') }}
        </flux:button>
    </div>

    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
        @forelse ($trainings as $training)
            @php
                $registrationStatus = $registrations[$training->id] ?? null;
                $isClosed = $training->registration_deadline?->isPast() ?? false;
                $badgeColor = match ($training->type) { 'Online' => 'green', 'Hybrid' => 'amber', default => 'blue' };
            @endphp
            <article wire:key="dashboard-training-{{ $training->id }}" class="grid gap-3 px-5 py-4 transition hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40">
                <div class="flex items-center gap-2">
                    <p class="text-lg font-bold leading-none text-indigo-600 dark:text-indigo-300">{{ $training->start_date->format('d') }}</p>
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ $training->start_date->format('M, Y') }}</p>
                    <p class="text-xs text-zinc-400">{{ $training->start_date->format('g:i A') }}</p>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="truncate font-semibold text-zinc-950 dark:text-white">{{ $training->title }}</p>
                        <flux:badge :color="$badgeColor" size="sm">{{ __($training->type) }}</flux:badge>
                        @if ($registrationStatus)<flux:badge color="indigo" size="sm">{{ __($registrationStatus) }}</flux:badge>@endif
                    </div>
                    <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $training->location_or_link ?: __('Venue will be announced') }}</p>
                </div>
                <flux:button class="w-full" wire:click="enroll({{ $training->id }})" variant="{{ $registrationStatus ? 'outline' : 'primary' }}" size="sm" icon="{{ $registrationStatus ? 'check' : 'academic-cap' }}" :disabled="$registrationStatus !== null || $isClosed">
                    {{ $registrationStatus ? __($registrationStatus) : ($isClosed ? __('Closed') : __('Register')) }}
                </flux:button>
            </article>
        @empty
            <div class="flex items-center gap-3 px-5 py-6 text-zinc-500 dark:text-zinc-400">
                <flux:icon.calendar-days class="size-6" />
                <span class="text-sm">{{ __('No upcoming training is scheduled. Please check the calendar later.') }}</span>
            </div>
        @endforelse
    </div>
</flux:card>
@else
<section class="flex flex-col gap-4" aria-labelledby="upcoming-trainings-heading">
    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
        <div>
            <flux:heading id="upcoming-trainings-heading" size="lg" class="font-bold">{{ __('Upcoming Trainings') }}</flux:heading>
            <flux:subheading>{{ __('Training opportunities available over the next :days days.', ['days' => $days]) }}</flux:subheading>
        </div>
        <flux:badge color="indigo" size="sm">{{ trans_choice(':count opportunity|:count opportunities', $trainings->count()) }}</flux:badge>
    </div>

    <div class="grid gap-4">
        @forelse ($trainings as $training)
            @php
                $registrationStatus = $registrations[$training->id] ?? null;
                $isEnrolled = $registrationStatus !== null;
                $isClosed = $training->registration_deadline?->isPast() ?? false;
                $badgeColor = match ($training->type) { 'Online' => 'green', 'Hybrid' => 'amber', default => 'blue' };
            @endphp
            <flux:card wire:key="upcoming-training-{{ $training->id }}" class="relative flex flex-col gap-4 overflow-hidden border-zinc-200 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-700">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-sky-400"></div>
                <div class="flex items-start justify-between gap-3 pt-1">
                    <flux:badge :color="$badgeColor" size="sm">{{ __($training->type) }}</flux:badge>
                    <div class="text-right">
                        <p class="text-sm font-bold text-zinc-800 dark:text-zinc-100">{{ $training->start_date->format('d M') }}</p>
                        <p class="text-xs text-zinc-500">{{ $training->start_date->format('g:i A') }}</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-2">
                    <flux:heading size="lg" class="leading-snug">{{ $training->title }}</flux:heading>
                    @if ($training->description)
                        <flux:text class="line-clamp-2 text-sm">{{ $training->description }}</flux:text>
                    @endif
                    <div class="mt-1 flex flex-col gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                        @if ($training->instructor_name)
                            <span class="flex items-center gap-1.5"><flux:icon.user class="size-4" />{{ $training->instructor_name }}</span>
                        @endif
                        @if ($training->location_or_link)
                            <span class="flex items-center gap-1.5"><flux:icon.map-pin class="size-4" /><span class="truncate">{{ $training->location_or_link }}</span></span>
                        @endif
                        @if ($training->capacity)
                            <span class="flex items-center gap-1.5"><flux:icon.user-group class="size-4" />{{ __(':selected of :capacity participants selected', ['selected' => $training->active_participants_count, 'capacity' => $training->capacity]) }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                    <flux:button href="{{ $training->googleCalendarUrl() }}" target="_blank" rel="noopener" variant="ghost" size="sm" icon="calendar-days">
                        {{ __('Add to calendar') }}
                    </flux:button>
                    <flux:button wire:click="enroll({{ $training->id }})" variant="primary" size="sm" icon="{{ $isEnrolled ? 'check' : 'academic-cap' }}" :disabled="$isEnrolled || $isClosed">
                        {{ $registrationStatus ? __($registrationStatus) : ($isClosed ? __('Closed') : __('Register')) }}
                    </flux:button>
                </div>
            </flux:card>
        @empty
            <flux:card>
                <div class="flex flex-col items-center gap-2 py-8 text-center">
                    <flux:icon.calendar-days class="size-9 text-zinc-400" />
                    <flux:heading>{{ __('No upcoming training is scheduled.') }}</flux:heading>
                    <flux:text>{{ __('Please check back later for new opportunities.') }}</flux:text>
                </div>
            </flux:card>
        @endforelse
    </div>
</section>
@endif
</div>

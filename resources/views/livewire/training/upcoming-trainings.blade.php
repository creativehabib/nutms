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
                $isFull = $training->capacity !== null && $training->active_participants_count >= $training->capacity;
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
                            <span class="flex items-center gap-1.5"><flux:icon.user-group class="size-4" />{{ __(':available of :capacity seats available', ['available' => max(0, $training->capacity - $training->active_participants_count), 'capacity' => $training->capacity]) }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                    <flux:button href="{{ $training->googleCalendarUrl() }}" target="_blank" rel="noopener" variant="ghost" size="sm" icon="calendar-days">
                        {{ __('Add to calendar') }}
                    </flux:button>
                    <flux:button wire:click="enroll({{ $training->id }})" variant="primary" size="sm" icon="{{ $isEnrolled ? 'check' : 'academic-cap' }}" :disabled="$isEnrolled || $isClosed || $isFull">
                        {{ $registrationStatus ? __($registrationStatus) : ($isClosed ? __('Closed') : ($isFull ? __('Full') : __('Register'))) }}
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

<div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 sm:p-6">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div class="flex flex-col gap-1">
            <flux:heading size="xl" class="font-bold tracking-tight">{{ __('Training Calendar') }}</flux:heading>
            <flux:subheading>{{ __('Explore National University teacher training schedules and register on time.') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <span class="size-2 rounded-full bg-emerald-500"></span>
            <flux:text class="text-sm">{{ __('Schedule updates automatically') }}</flux:text>
        </div>
    </div>

    <livewire:training.upcoming-trainings :days="30" />

    <flux:card class="overflow-hidden p-0 shadow-sm">
        <div class="flex flex-col gap-4 border-b border-zinc-200 bg-zinc-50/70 px-4 py-4 dark:border-zinc-700 dark:bg-zinc-800/50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">
                    <flux:icon.calendar-days class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg" class="font-bold">{{ $month->format('F Y') }}</flux:heading>
                    <flux:text class="text-xs">{{ trans_choice(':count scheduled training|:count scheduled trainings', $trainingsByDate->flatten(1)->count()) }}</flux:text>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <flux:select wire:model.live="type" size="sm" class="min-w-32" aria-label="{{ __('Filter by training type') }}">
                    <option value="All">{{ __('All types') }}</option>
                    <option value="Online">{{ __('Online') }}</option>
                    <option value="Offline">{{ __('Offline') }}</option>
                    <option value="Hybrid">{{ __('Hybrid') }}</option>
                </flux:select>
                <flux:button variant="ghost" size="sm" icon="chevron-left" wire:click="previousMonth" :aria-label="__('Previous month')" />
                <flux:button variant="outline" size="sm" wire:click="goToToday">{{ __('Today') }}</flux:button>
                <flux:button variant="ghost" size="sm" icon="chevron-right" wire:click="nextMonth" :aria-label="__('Next month')" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="grid min-w-[720px] grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                    <div wire:key="weekday-{{ $weekday }}" class="bg-zinc-50 py-2.5 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        {{ __($weekday) }}
                    </div>
                @endforeach

                @for ($blank = 0; $blank < $firstDayOfWeek; $blank++)
                    <div class="min-h-32 bg-zinc-50/70 dark:bg-zinc-800/40"></div>
                @endfor

                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = $month->setDay($day);
                        $dateKey = $date->toDateString();
                        $dayTrainings = $trainingsByDate->get($dateKey, collect());
                    @endphp
                    <div wire:key="calendar-day-{{ $dateKey }}" class="group min-h-32 bg-white p-2.5 transition hover:bg-indigo-50/40 dark:bg-zinc-900 dark:hover:bg-indigo-950/20">
                        <span @class([
                            'flex size-7 items-center justify-center rounded-full text-sm font-semibold',
                            'bg-indigo-600 text-white shadow-sm' => $date->isToday(),
                            'text-zinc-700 dark:text-zinc-300' => ! $date->isToday(),
                        ])>{{ $day }}</span>

                        <div class="mt-2 flex flex-col gap-1.5">
                            @foreach ($dayTrainings as $training)
                                <div wire:key="calendar-training-{{ $training->id }}" class="rounded-md border border-indigo-100 bg-indigo-50 px-2 py-1.5 dark:border-indigo-500/20 dark:bg-indigo-500/10" title="{{ $training->title }}">
                                    <p class="truncate text-xs font-semibold text-indigo-700 dark:text-indigo-300">{{ $training->title }}</p>
                                    <p class="mt-0.5 text-[10px] text-indigo-500 dark:text-indigo-400">{{ $training->start_date->format('g:i A') }} · {{ __($training->type) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor

                @php($remainingCells = (7 - (($firstDayOfWeek + $daysInMonth) % 7)) % 7)
                @for ($blank = 0; $blank < $remainingCells; $blank++)
                    <div class="min-h-32 bg-zinc-50/70 dark:bg-zinc-800/40"></div>
                @endfor
            </div>
        </div>
    </flux:card>
</div>

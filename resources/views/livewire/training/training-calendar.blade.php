<div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 sm:p-6 lg:p-8">

    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-1.5">
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">{{ __('Training Calendar') }}</flux:heading>
            <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ __('Explore National University teacher training schedules and register on time.') }}</flux:subheading>
        </div>

        <!-- Live Status Pill -->
        <div class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400">
            <span class="relative flex size-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
            </span>
            {{ __('Live Schedule') }}
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_22rem] xl:grid-cols-[minmax(0,1fr)_24rem]">

        <!-- Calendar Card -->
        <flux:card class="order-2 flex min-w-0 flex-col overflow-hidden p-0 shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 lg:order-1">

            <!-- Calendar Toolbar -->
            <div class="flex flex-col gap-4 border-b border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800/80 dark:bg-zinc-800/30 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-5">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shadow-sm ring-1 ring-inset ring-indigo-500/10 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                        <flux:icon.calendar-days class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold text-zinc-900 dark:text-white">{{ $month->format('F Y') }}</flux:heading>
                        <flux:text class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ trans_choice(':count scheduled training|:count scheduled trainings', $trainingsByDate->flatten(1)->count()) }}</flux:text>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <flux:select wire:model.live="type" size="sm" class="min-w-[130px]" aria-label="{{ __('Filter by training type') }}">
                        <option value="All">{{ __('All types') }}</option>
                        <option value="Online">{{ __('Online') }}</option>
                        <option value="Offline">{{ __('Offline') }}</option>
                        <option value="Hybrid">{{ __('Hybrid') }}</option>
                    </flux:select>

                    <!-- Segmented Navigation Buttons -->
                    <div class="flex items-center rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/50 dark:bg-zinc-900">
                        <flux:button variant="ghost" size="sm" icon="chevron-left" wire:click="previousMonth" :aria-label="__('Previous month')" class="rounded-r-none border-r border-zinc-200 dark:border-zinc-700/50" />
                        <flux:button variant="ghost" size="sm" wire:click="goToToday" class="rounded-none font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800/50">{{ __('Today') }}</flux:button>
                        <flux:button variant="ghost" size="sm" icon="chevron-right" wire:click="nextMonth" :aria-label="__('Next month')" class="rounded-l-none border-l border-zinc-200 dark:border-zinc-700/50" />
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="overflow-x-auto">
                <div class="isolate flex min-w-[720px] flex-col bg-zinc-200 dark:bg-zinc-700/50">

                    <!-- Days of Week Header -->
                    <div class="grid grid-cols-7 gap-px">
                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                            <div wire:key="weekday-{{ $weekday }}" class="bg-zinc-50 py-3 text-center text-xs font-bold uppercase tracking-wider text-zinc-500 dark:bg-zinc-800/80 dark:text-zinc-400">
                                {{ __($weekday) }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Calendar Days Body -->
                    <div class="mt-px grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700/50">

                        <!-- Padding Days (Prev Month) -->
                        @for ($blank = 0; $blank < $firstDayOfWeek; $blank++)
                            <div class="min-h-[120px] bg-zinc-50/40 p-2.5 dark:bg-zinc-800/20"></div>
                        @endfor

                        <!-- Actual Days -->
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = $month->setDay($day);
                                $dateKey = $date->toDateString();
                                $dayTrainings = $trainingsByDate->get($dateKey, collect());
                                $isToday = $date->isToday();
                            @endphp

                            <div wire:key="calendar-day-{{ $dateKey }}" class="group relative min-h-[120px] bg-white p-2.5 transition-colors hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">

                                <!-- Day Number -->
                                <div class="flex items-center justify-between">
                                    <span @class([
                                        'flex size-7 items-center justify-center rounded-full text-sm font-semibold tracking-tight transition-colors',
                                        'bg-indigo-600 text-white shadow-md ring-2 ring-indigo-600/20 ring-offset-1 dark:ring-offset-zinc-900' => $isToday,
                                        'text-zinc-900 group-hover:bg-zinc-100 dark:text-zinc-100 dark:group-hover:bg-zinc-800' => ! $isToday,
                                    ])>{{ $day }}</span>

                                    <!-- Mobile Indicator Dot (if items overflow) -->
                                    @if($dayTrainings->isNotEmpty())
                                        <span class="flex size-1.5 rounded-full bg-indigo-500 lg:hidden"></span>
                                    @endif
                                </div>

                                <!-- Training Events -->
                                <div class="no-scrollbar flex max-h-[190px] flex-col gap-1.5 overflow-y-auto">
                                    @foreach ($dayTrainings as $training)
                                        <div wire:key="calendar-training-{{ $training->id }}"
                                             class="relative flex flex-col gap-0.5 rounded-r-lg rounded-l-sm border-l-2 border-indigo-500 bg-indigo-50 px-2.5 py-1.5 shadow-[0_1px_2px_rgba(0,0,0,0.02)] transition-all hover:bg-indigo-100 hover:translate-x-0.5 dark:border-indigo-400 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20"
                                             title="{{ $training->title }}">

                                            <p class="truncate text-[12px] font-semibold text-zinc-900 dark:text-zinc-100">{{ $training->title }}</p>

                                            <div class="flex items-center justify-between gap-1 text-[10px] font-medium text-zinc-500 dark:text-zinc-400">
                                                <span class="truncate">{{ $training->start_date->format('g:i A') }}</span>
                                                <span class="flex shrink-0 items-center gap-1 opacity-80">
                                                    @if($training->type === 'Online')
                                                        <flux:icon.video-camera variant="micro" class="size-3" />
                                                    @elseif($training->type === 'Offline')
                                                        <flux:icon.map-pin variant="micro" class="size-3" />
                                                    @else
                                                        <flux:icon.arrow-path variant="micro" class="size-3" />
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endfor

                        <!-- Padding Days (Next Month) -->
                        @php($remainingCells = (7 - (($firstDayOfWeek + $daysInMonth) % 7)) % 7)
                        @for ($blank = 0; $blank < $remainingCells; $blank++)
                            <div class="min-h-[120px] bg-zinc-50/40 p-2.5 dark:bg-zinc-800/20"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </flux:card>

        <!-- Upcoming Trainings Sidebar -->
        <aside class="order-1 min-w-0 lg:order-2 lg:sticky lg:top-6">
            <livewire:training.upcoming-trainings :days="30" />
        </aside>
    </div>
</div>

<div class="mx-auto w-full max-w-7xl p-4 sm:p-6">
    <div class="flex flex-col gap-6 lg:flex-row">

        <!-- ========================================== -->
        <!-- Left Side: Custom Calendar Grid -->
        <!-- ========================================== -->
        <div class="flex-1">
            <flux:card class="p-0 overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">

                <!-- Calendar Header -->
                <div class="flex items-center justify-between border-b border-zinc-200 bg-zinc-50/80 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                            <flux:icon.calendar class="size-5" />
                        </div>
                        <flux:heading size="lg" class="font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $currentDate->format('F Y') }}
                        </flux:heading>
                    </div>

                    <div class="flex items-center gap-2">
                        <flux:button variant="ghost" size="sm" icon="chevron-left" wire:click="prevMonth" />
                        <flux:button variant="outline" size="sm" wire:click="mount">{{ __('Today') }}</flux:button>
                        <flux:button variant="ghost" size="sm" icon="chevron-right" wire:click="nextMonth" />
                    </div>
                </div>

                <!-- Calendar Grid (Tailwind) -->
                <div class="bg-zinc-200 dark:bg-zinc-700 grid grid-cols-7 gap-px">

                    <!-- Days of Week Headers -->
                    @php
                        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    @endphp
                    @foreach($daysOfWeek as $day)
                        <div class="bg-zinc-50 py-2 text-center text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ $day }}
                        </div>
                    @endforeach

                    <!-- Blank Spaces (Before month starts) -->
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="bg-zinc-50/50 dark:bg-zinc-800/30 min-h-[100px] sm:min-h-[120px]"></div>
                    @endfor

                    <!-- Actual Days of Month -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateString = $currentDate->copy()->day($day)->format('Y-m-d');
                            $isToday = $dateString === \Carbon\Carbon::today()->format('Y-m-d');
                            $dayTrainings = $trainings->has($dateString) ? $trainings[$dateString] : [];
                        @endphp

                        <div class="bg-white dark:bg-zinc-900 min-h-[100px] sm:min-h-[120px] p-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors relative group">

                            <!-- Date Number -->
                            <div class="flex justify-between items-start">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full text-sm font-medium {{ $isToday ? 'bg-indigo-600 text-white shadow-md' : 'text-zinc-700 dark:text-zinc-300' }}">
                                    {{ $day }}
                                </span>
                            </div>

                            <!-- Render Trainings for this day -->
                            <div class="mt-2 flex flex-col gap-1.5">
                                @foreach($dayTrainings as $training)
                                    <div class="rounded bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 cursor-pointer hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition truncate" title="{{ $training['title'] }}">
                                        • {{ $training['title'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor

                    <!-- Blank Spaces (After month ends to complete grid) -->
                    @php
                        $totalCells = $firstDayOfWeek + $daysInMonth;
                        $remainingCells = 7 - ($totalCells % 7);
                        if ($remainingCells == 7) $remainingCells = 0;
                    @endphp
                    @for($i = 0; $i < $remainingCells; $i++)
                        <div class="bg-zinc-50/50 dark:bg-zinc-800/30 min-h-[100px] sm:min-h-[120px]"></div>
                    @endfor

                </div>
            </flux:card>
        </div>

        <!-- ========================================== -->
        <!-- Right Side: Upcoming Trainings Sidebar -->
        <!-- ========================================== -->
        <div class="w-full lg:w-80 xl:w-96 shrink-0 flex flex-col gap-4">
            <flux:heading size="lg" class="font-bold flex items-center gap-2 text-zinc-900 dark:text-zinc-100">
                <flux:icon.rocket-launch class="size-5 text-indigo-500" />
                {{ __('Upcoming Trainings') }}
            </flux:heading>

            <!-- Upcoming Training Cards (Demo Layout) -->
            <flux:card class="p-4 shadow-sm border border-zinc-200 dark:border-zinc-700 hover:border-indigo-300 transition-colors cursor-pointer relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                <div class="flex justify-between items-start mb-2">
                    <flux:badge color="indigo" size="sm" class="font-medium">Online</flux:badge>
                    <span class="text-xs font-semibold text-zinc-500">12 Aug, 2026</span>
                </div>
                <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base leading-tight mb-1">Digital Pedagogy & Modern Teaching</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4 line-clamp-2">Learn how to integrate modern digital tools into your daily classroom activities.</p>
                <flux:button variant="primary" size="sm" class="w-full">{{ __('View Details') }}</flux:button>
            </flux:card>

            <flux:card class="p-4 shadow-sm border border-zinc-200 dark:border-zinc-700 hover:border-indigo-300 transition-colors cursor-pointer relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
                <div class="flex justify-between items-start mb-2">
                    <flux:badge color="emerald" size="sm" class="font-medium">Offline</flux:badge>
                    <span class="text-xs font-semibold text-zinc-500">24 Aug, 2026</span>
                </div>
                <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base leading-tight mb-1">Curriculum Development Workshop</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4 line-clamp-2">A comprehensive workshop on the new national curriculum framework.</p>
                <flux:button variant="outline" size="sm" class="w-full">{{ __('Register Now') }}</flux:button>
            </flux:card>

            <!-- View All Button -->
            <flux:button variant="ghost" class="w-full text-indigo-600 hover:text-indigo-700">
                {{ __('View All Trainings &rarr;') }}
            </flux:button>
        </div>

    </div>
</div>

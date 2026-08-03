<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <header class="relative overflow-hidden rounded-2xl bg-linear-to-br from-indigo-700 via-indigo-600 to-sky-500 px-6 py-7 text-white shadow-lg sm:px-8">
            <div class="absolute -end-16 -top-20 size-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-24 end-28 size-48 rounded-full bg-sky-300/20"></div>
            <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                        <flux:icon.chart-bar-square class="size-4" />
                        {{ auth()->user()->isAdmin() ? __('Institution and Teacher Overview') : (auth()->user()->role === \App\Enums\UserRole::Principal ? __('College Management') : __('Teacher Services')) }}
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ auth()->user()->isAdmin() ? __('Admin Dashboard') : (auth()->user()->role === \App\Enums\UserRole::Principal ? __('Principal Dashboard') : __('Teacher Dashboard')) }}</h1>
                    <p class="mt-2 max-w-2xl text-sm text-indigo-100 sm:text-base">{{ auth()->user()->isAdmin() ? __('Training information') : (auth()->user()->role === \App\Enums\UserRole::Principal ? __('Teacher information') : __('Teacher information')) }}</p>
                </div>

                @if (auth()->user()->isAdmin() && $report['lastUpdatedAt'])
                    <div class="flex items-center gap-2 text-xs text-indigo-100">
                        <flux:icon.clock class="size-4" />
                        Latest update: {{ $report['lastUpdatedAt'] }}
                    </div>
                @endif
            </div>
        </header>

        @if(auth()->user()->role === \App\Enums\UserRole::Principal && ! auth()->user()->isApproved())
            <flux:card><flux:heading size="lg">{{ __('Approval information') }}</flux:heading><flux:callout class="mt-4" variant="warning" :heading="__('Information')">{{ __('Teacher information') }}</flux:callout></flux:card>
        @elseif(auth()->user()->role === \App\Enums\UserRole::Principal)
            <div class="grid gap-5 md:grid-cols-3">
                <flux:card class="flex flex-col"><div class="flex items-center gap-3"><div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><flux:icon.identification class="size-6" /></div><flux:heading size="lg">{{ __('My Profile') }}</flux:heading></div><flux:text class="mt-3">{{ __('Training information') }}</flux:text>@if(auth()->user()->teacherProfile)<flux:button class="mt-5" variant="primary" :href="route('teachers.show', auth()->user()->teacherProfile)" wire:navigate>{{ __('Profile information') }}</flux:button>@else<flux:callout class="mt-5" variant="warning">{{ __('Teacher information') }}</flux:callout>@endif</flux:card>
                <flux:card class="flex flex-col"><div class="flex items-center gap-3"><div class="rounded-lg bg-sky-50 p-2.5 text-sky-600 dark:bg-sky-950 dark:text-sky-300"><flux:icon.building-library class="size-6" /></div><flux:heading size="lg">{{ __('College Profile') }}</flux:heading></div><flux:text class="mt-3">{{ __('College information') }}</flux:text><flux:button class="mt-5" :href="route('colleges.show', auth()->user()->college_id)" wire:navigate>{{ __('College information') }}</flux:button></flux:card>
                <flux:card class="flex flex-col"><div class="flex items-center gap-3"><div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><flux:icon.user-group class="size-6" /></div><flux:heading size="lg">{{ __('Teacher information') }}</flux:heading></div><flux:text class="mt-3"> College {{ number_format($report['totalTeachers']) }} teachers Search,    ।</flux:text><flux:button class="mt-5" :href="route('teachers.manage')" wire:navigate>{{ __('Teacher information') }}</flux:button></flux:card>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Teachers') }}</span><flux:icon.user-group class="size-5 text-indigo-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalTeachers']) }}</p></div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Training information') }}</span><flux:icon.academic-cap class="size-5 text-emerald-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['teachersWithIctTraining']) }}</p><p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($report['ictTrainingCoverage'], 1) }}% coverage</p></div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Information') }}</span><flux:icon.user-minus class="size-5 text-rose-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($principalStats['retired']->count()) }}</p></div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Information') }}</span><flux:icon.clock class="size-5 text-amber-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($principalStats['upcomingRetirements']->count()) }}</p></div>
            </div>
            <div class="grid gap-5 lg:grid-cols-2">
                <flux:card><flux:heading size="lg">{{ __('Teacher information') }}</flux:heading><div class="mt-4 grid gap-3">@forelse($principalStats['subjects'] as $subject)<div class="flex items-center justify-between gap-4 rounded-lg bg-zinc-50 px-4 py-3 dark:bg-zinc-800"><span>{{ $subject->subject }}</span><flux:badge color="indigo">{{ $subject->teachers_count }} teachers</flux:badge></div>@empty<flux:text>{{ __('Information') }}</flux:text>@endforelse</div></flux:card>
                <flux:card><flux:heading size="lg">{{ __('Training information') }}</flux:heading><div class="mt-4 grid gap-3">@forelse($principalStats['trainings'] as $training)<div class="flex items-center justify-between gap-4 rounded-lg bg-zinc-50 px-4 py-3 dark:bg-zinc-800"><span>{{ $training['name'] }}</span><flux:badge color="emerald">{{ $training['count'] }} teachers</flux:badge></div>@empty<flux:text>{{ __('Training information') }}</flux:text>@endforelse</div></flux:card>
                <flux:card class="lg:col-span-2"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><flux:heading size="lg">{{ __('Information') }}</flux:heading><flux:text>Current retirement age {{ $principalStats['retirementAge'] }} years।</flux:text></div><div class="flex gap-2"><flux:badge color="red">Retired {{ $principalStats['retired']->count() }} teachers</flux:badge><flux:badge color="amber">In the next 1 year {{ $principalStats['upcomingRetirements']->count() }} teachers</flux:badge></div></div>@if($principalStats['missingBirthDates'] > 0)<flux:callout class="mt-4" variant="warning">{{ $principalStats['missingBirthDates'] }} teachers are missing birth dates.</flux:callout>@endif<div class="mt-4 grid gap-4 md:grid-cols-2"><div><p class="mb-2 text-sm font-semibold">{{ __('Information') }}</p>@forelse($principalStats['retired'] as $row)<div class="flex justify-between gap-3 border-b border-zinc-200 py-2 text-sm dark:border-zinc-700"><span>{{ $row['name'] }}</span><span>{{ $row['retirement_date']->format('d M Y') }}</span></div>@empty<flux:text>{{ __('Information') }}</flux:text>@endforelse</div><div><p class="mb-2 text-sm font-semibold">{{ __('Information') }}</p>@forelse($principalStats['upcomingRetirements'] as $row)<div class="flex justify-between gap-3 border-b border-zinc-200 py-2 text-sm dark:border-zinc-700"><span>{{ $row['name'] }}</span><span>{{ $row['retirement_date']->format('d M Y') }}</span></div>@empty<flux:text>{{ __('Information') }}</flux:text>@endforelse</div></div></flux:card>
            </div>
        @elseif(auth()->user()->role === \App\Enums\UserRole::Teacher)
            @if($teacherStats)
                <flux:card class="overflow-hidden border-indigo-200 bg-linear-to-r from-indigo-50 to-sky-50 dark:border-indigo-900 dark:from-indigo-950/50 dark:to-sky-950/40">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="max-w-2xl">
                            <div class="flex items-center gap-3"><div class="rounded-xl bg-indigo-600 p-2.5 text-white shadow-sm"><flux:icon.clipboard-document-check class="size-6" /></div><div><flux:heading size="lg">Your profile {{ $teacherStats['completeness']['percentage'] }}% complete</flux:heading><flux:text>{{ $teacherStats['completeness']['completed'] }}items completed, {{ $teacherStats['completeness']['total'] - $teacherStats['completeness']['completed'] }}items still need to be added.</flux:text></div></div>
                            <div class="mt-4 h-3 overflow-hidden rounded-full bg-white ring-1 ring-indigo-100 dark:bg-zinc-800 dark:ring-indigo-900" role="progressbar" :aria-label="__('Profile information')" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $teacherStats['completeness']['percentage'] }}"><div class="h-full rounded-full bg-linear-to-r from-indigo-600 to-sky-500 transition-all duration-500" style="width: {{ $teacherStats['completeness']['percentage'] }}%"></div></div>
                            @if($teacherStats['completeness']['percentage'] < 100)<div class="mt-4 flex flex-wrap gap-2"><span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{ __('Information') }}</span>@foreach($teacherStats['completeness']['missing']->take(6) as $field)<flux:badge color="amber">{{ $field }}</flux:badge>@endforeach @if($teacherStats['completeness']['missing']->count() > 6)<flux:badge color="zinc">More {{ $teacherStats['completeness']['missing']->count() - 6 }}items</flux:badge>@endif</div>@endif
                        </div>
                        @if($teacherStats['completeness']['percentage'] < 100)
                            <div class="lg:max-w-sm"><flux:callout variant="warning" :heading="__('Profile information')">{{ __('Training information') }}</flux:callout>@if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved)<flux:button class="mt-3 w-full" variant="primary" :href="route('teachers.edit', $teacherStats['profile'])" icon="pencil-square" wire:navigate>{{ __('Information') }}</flux:button>@else<flux:text class="mt-3 text-xs">{{ __('Approval information') }}</flux:text>@endif</div>
                        @else
                            <flux:badge color="green" size="lg">{{ __('Profile information') }}</flux:badge>
                        @endif
                    </div>
                </flux:card>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Profile information') }}</span><flux:icon.shield-check class="size-5 text-indigo-500" /></div>
                        <div class="mt-4"><flux:badge :color="$teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved ? 'green' : 'amber'">{{ $teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved ? __('Approval information') : __('Approval information') }}</flux:badge></div>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Information') }}</span><flux:icon.calendar-days class="size-5 text-sky-500" /></div>
                        @if($teacherStats['retirementDate'])<p class="mt-3 text-xl font-bold text-zinc-950 dark:text-white">{{ $teacherStats['retirementDate']->format('d M Y') }}</p><p class="mt-1 text-xs {{ $teacherStats['isRetired'] ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $teacherStats['isRetired'] ? __('Information') : number_format($teacherStats['daysUntilRetirement']) . __('Information') }}</p>@else<flux:text class="mt-3">{{ __('Information') }}</flux:text>@endif
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Training information') }}</span><flux:icon.academic-cap class="size-5 text-emerald-500" /></div>
                        <p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($teacherStats['trainings']->count()) }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Training information') }}</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Information') }}</span><flux:icon.clock class="size-5 text-violet-500" /></div>
                        <p class="mt-3 text-sm font-semibold text-zinc-950 dark:text-white">{{ $teacherStats['lastUpdatedAt'] ?? __('Information') }}</p>
                    </div>
                </div>

                @if($teacherStats['profile']->approval_status !== \App\Enums\ApprovalStatus::Approved)
                    <flux:callout variant="warning" :heading="__('Approval information')">{{ __('College information') }}</flux:callout>
                @endif

                <div class="grid gap-5 lg:grid-cols-3">
                    <flux:card class="lg:col-span-2">
                        <div class="flex items-start justify-between gap-4"><div><flux:heading size="lg">{{ __('Training information') }}</flux:heading><flux:text class="mt-1">{{ __('Training information') }}</flux:text></div><div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><flux:icon.academic-cap class="size-6" /></div></div>
                        <div class="mt-5 grid gap-3">@forelse($teacherStats['trainings'] as $training)<div class="flex flex-col justify-between gap-2 rounded-xl border border-zinc-200 px-4 py-3 sm:flex-row sm:items-center dark:border-zinc-700"><div><p class="font-semibold text-zinc-950 dark:text-white">{{ $training['name'] }}</p><p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $training['institute'] ?: __('Information') }}</p></div>@if($training['year'])<flux:badge color="emerald">{{ $training['year'] }}</flux:badge>@endif</div>@empty<flux:callout variant="warning">{{ __('Training information') }}</flux:callout>@endforelse</div>
                    </flux:card>
                    <flux:card class="flex flex-col">
                        <div class="flex items-center gap-3"><div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><flux:icon.identification class="size-6" /></div><flux:heading size="lg">{{ __('My Profile') }}</flux:heading></div>
                        <div class="mt-4 grid gap-3 text-sm"><div class="flex justify-between gap-3"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Information') }}</span><span class="font-medium text-zinc-950 dark:text-white">{{ $teacherStats['profile']->subject ?: __('Not added') }}</span></div><div class="flex justify-between gap-3"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Designation') }}</span><span class="font-medium text-zinc-950 dark:text-white">{{ $teacherStats['profile']->designation ?: __('Not added') }}</span></div><div class="flex justify-between gap-3"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Information') }}</span><span class="font-medium text-zinc-950 dark:text-white">{{ $teacherStats['retirementAge'] }} years</span></div></div>
                        @if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved)<flux:button class="mt-6" variant="primary" :href="route('teachers.show', $teacherStats['profile'])" wire:navigate>{{ __('Profile information') }}</flux:button>@endif
                    </flux:card>
                </div>
            @else
                <flux:card><flux:heading size="lg">{{ __('Teacher information') }}</flux:heading><flux:text class="mt-2">{{ __('College information') }}</flux:text><flux:button class="mt-4" variant="primary" :href="route('teachers.create')" wire:navigate>{{ __('Profile information') }}</flux:button></flux:card>
            @endif
        @elseif(auth()->user()->isAdmin())
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><flux:icon.building-library class="size-6" /></div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('All Institutions') }}</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalColleges']) }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Colleges') }}</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-sky-50 p-2.5 text-sky-600 dark:bg-sky-950 dark:text-sky-300"><flux:icon.user-group class="size-6" /></div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('All Teachers') }}</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalTeachers']) }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Teachers') }}</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><flux:icon.computer-desktop class="size-6" /></div>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ __('College information') }}</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalComputers']) }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Computers') }}</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-violet-50 p-2.5 text-violet-600 dark:bg-violet-950 dark:text-violet-300"><flux:icon.academic-cap class="size-6" /></div>
                    <span class="text-xs font-medium text-violet-600 dark:text-violet-400">{{ __('Training information') }}</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['ictTrainingCoverage'], 1) }}%</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('ICT Training Coverage') }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section aria-labelledby="college-report-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading id="college-report-heading" size="lg">{{ __('Computer Lab Report') }}</flux:heading>
                        <flux:text class="mt-1">{{ __('College information') }}</flux:text>
                    </div>
                    @if(auth()->user()->isAdmin())<flux:button :href="route('lab.summary')" variant="ghost" size="sm" icon-trailing="arrow-up-right" wire:navigate>{{ __('Details') }}</flux:button>@endif
                </div>

                <div class="mt-7 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-4xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['labCoverage'], 1) }}%</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('College information') }}</p>
                    </div>
                    <div class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ number_format($report['collegesWithLab']) }} / {{ number_format($report['totalColleges']) }}</div>
                </div>

                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-rose-100 dark:bg-rose-950">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $report['labCoverage'] }}%"></div>
                </div>

                <div class="mt-6 grid grid-cols-2 divide-x divide-zinc-200 rounded-xl bg-zinc-50 py-4 dark:divide-zinc-700 dark:bg-zinc-800/60">
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-emerald-500"></span>{{ __('Lab information') }}</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['collegesWithLab']) }}</p>
                    </div>
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-rose-500"></span>{{ __('Lab information') }}</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['collegesWithoutLab']) }}</p>
                    </div>
                </div>
            </section>

            <section aria-labelledby="training-report-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading id="training-report-heading" size="lg">{{ __('Training information') }}</flux:heading>
                        <flux:text class="mt-1">{{ __('Teacher information') }}</flux:text>
                    </div>
                    @if(auth()->user()->isAdmin())<flux:button :href="route('ict.summary')" variant="ghost" size="sm" icon-trailing="arrow-up-right" wire:navigate>{{ __('Details') }}</flux:button>@endif
                </div>

                <div class="mt-7 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-4xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['ictTrainingCoverage'], 1) }}%</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Training information') }}</p>
                    </div>
                    <div class="rounded-full bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-700 dark:bg-sky-950 dark:text-sky-300">{{ number_format($report['teachersWithIctTraining']) }} / {{ number_format($report['totalTeachers']) }}</div>
                </div>

                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-amber-100 dark:bg-amber-950">
                    <div class="h-full rounded-full bg-sky-500" style="width: {{ $report['ictTrainingCoverage'] }}%"></div>
                </div>

                <div class="mt-6 grid grid-cols-2 divide-x divide-zinc-200 rounded-xl bg-zinc-50 py-4 dark:divide-zinc-700 dark:bg-zinc-800/60">
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-sky-500"></span>{{ __('Training information') }}</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithIctTraining']) }}</p>
                    </div>
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-amber-500"></span>{{ __('Training information') }}</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithoutIctTraining']) }}</p>
                    </div>
                </div>
            </section>
        </div>
        @endif
    </div>
</x-layouts::app>

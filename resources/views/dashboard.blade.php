<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">

        <!-- ======================================= -->
        <!-- HEADER SECTION (Common for all roles)   -->
        <!-- ======================================= -->
        <header class="relative overflow-hidden rounded-2xl bg-linear-to-br from-indigo-700 via-indigo-600 to-sky-500 px-6 py-7 text-white shadow-lg sm:px-8">
            <!-- Background Ornaments -->
            <div class="absolute -end-16 -top-20 size-56 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-24 end-28 size-48 rounded-full bg-sky-300/20 blur-xl"></div>

            <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold ring-1 ring-white/20 backdrop-blur-sm">
                        <flux:icon.chart-bar-square class="size-4" />
                        {{ auth()->user()->isAdmin() ? __('Institution and Teacher Overview') : (auth()->user()->hasRole('principal') ? __('College Management') : __('Teacher Services')) }}
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ auth()->user()->isAdmin() ? __('Admin Dashboard') : (auth()->user()->hasRole('principal') ? __('Principal Dashboard') : __('Teacher Dashboard')) }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-indigo-100 sm:text-base">
                        {{ auth()->user()->isAdmin() ? __('Monitor institutional training, labs, and teacher data from one place.') : (auth()->user()->hasRole('principal') ? __('Track your college profile, teachers, and approval status.') : __('Manage your teacher profile, training records, and retirement timeline.')) }}
                    </p>
                </div>

                @if (auth()->user()->isAdmin() && $report['lastUpdatedAt'])
                    <div class="flex items-center gap-2 text-xs font-medium text-indigo-100 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm">
                        <flux:icon.clock class="size-4" />
                        Latest update: {{ $report['lastUpdatedAt'] }}
                    </div>
                @endif
            </div>
        </header>

        <!-- ======================================= -->
        <!-- PRINCIPAL DASHBOARD                     -->
        <!-- ======================================= -->
        @if(auth()->user()->hasRole('principal') && !auth()->user()->isApproved())
            <flux:card class="border-amber-200 dark:border-amber-500/20 bg-amber-50/50 dark:bg-amber-500/5">
                <flux:heading size="lg">{{ __('Approval Pending') }}</flux:heading>
                <flux:callout class="mt-4" variant="warning" :heading="__('Account awaiting approval')">
                    {{ __('Your principal account must be approved before you can manage college records.') }}
                </flux:callout>
            </flux:card>

        @elseif(auth()->user()->hasRole('principal'))

            <!-- Principal Top Actions -->
            <div class="grid gap-5 md:grid-cols-3">
                <flux:card class="flex flex-col justify-between hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                <flux:icon.identification class="size-6" />
                            </div>
                            <flux:heading size="lg">{{ __('My Profile') }}</flux:heading>
                        </div>
                        <flux:text class="mt-3 text-zinc-500 dark:text-zinc-400">{{ __('View your linked teacher profile and approval details.') }}</flux:text>
                    </div>
                    @if(auth()->user()->teacherProfile)
                        <flux:button class="mt-5 w-full" variant="primary" :href="route('teachers.show', auth()->user()->teacherProfile)" wire:navigate>{{ __('View Profile') }}</flux:button>
                    @else
                        <flux:callout class="mt-5" variant="warning">{{ __('No teacher profile is linked to your account yet.') }}</flux:callout>
                    @endif
                </flux:card>

                <flux:card class="flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-600 transition-colors">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-sky-50 p-2.5 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                                <flux:icon.building-library class="size-6" />
                            </div>
                            <flux:heading size="lg">{{ __('College Profile') }}</flux:heading>
                        </div>
                        <flux:text class="mt-3 text-zinc-500 dark:text-zinc-400">{{ __('Review your college profile, contact details, and lab information.') }}</flux:text>
                    </div>
                    <flux:button class="mt-5 w-full" :href="route('colleges.show', auth()->user()->college_id)" wire:navigate>{{ __('View College Profile') }}</flux:button>
                </flux:card>

                <flux:card class="flex flex-col justify-between hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon.user-group class="size-6" />
                            </div>
                            <flux:heading size="lg">{{ __('College Teachers') }}</flux:heading>
                        </div>
                        <flux:text class="mt-3 text-zinc-500 dark:text-zinc-400">{{ __('Search and manage :count teachers assigned to your college.', ['count' => number_format($report['totalTeachers'])]) }}</flux:text>
                    </div>
                    <flux:button class="mt-5 w-full" :href="route('teachers.manage')" wire:navigate>{{ __('Manage Teachers') }}</flux:button>
                </flux:card>
            </div>

            <!-- Principal Stats Grid -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Teachers') }}</span>
                        <flux:icon.user-group class="size-5 text-indigo-500 dark:text-indigo-400" />
                    </div>
                    <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['totalTeachers']) }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('ICT Trained Teachers') }}</span>
                        <flux:icon.academic-cap class="size-5 text-emerald-500 dark:text-emerald-400" />
                    </div>
                    <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithIctTraining']) }}</p>
                    <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($report['ictTrainingCoverage'], 1) }}% coverage</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Retired Teachers') }}</span>
                        <flux:icon.user-minus class="size-5 text-rose-500 dark:text-rose-400" />
                    </div>
                    <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($principalStats['retired']->count()) }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Upcoming Retirements') }}</span>
                        <flux:icon.clock class="size-5 text-amber-500 dark:text-amber-400" />
                    </div>
                    <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($principalStats['upcomingRetirements']->count()) }}</p>
                </div>
            </div>

            <!-- Principal Data Reports -->
            <div class="grid gap-5 lg:grid-cols-2">
                <flux:card>
                    <flux:heading size="lg">{{ __('Teachers by Subject') }}</flux:heading>
                    <div class="mt-4 grid gap-3">
                        @forelse($principalStats['subjects'] as $subject)
                            <div class="flex items-center justify-between gap-4 rounded-lg bg-zinc-50 px-4 py-3 border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-700/50">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $subject->name }}</span>
                                <flux:badge color="indigo">{{ $subject->teachers_count }} teachers</flux:badge>
                            </div>
                        @empty
                            <flux:text>{{ __('No subject statistics are available yet.') }}</flux:text>
                        @endforelse
                    </div>
                </flux:card>

                <flux:card>
                    <flux:heading size="lg">{{ __('ICT Training Summary') }}</flux:heading>
                    <div class="mt-4 grid gap-3">
                        @forelse($principalStats['trainings'] as $training)
                            <div class="flex items-center justify-between gap-4 rounded-lg bg-zinc-50 px-4 py-3 border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-700/50">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $training['name'] }}</span>
                                <flux:badge color="emerald">{{ $training['count'] }} teachers</flux:badge>
                            </div>
                        @empty
                            <flux:text>{{ __('No training records are available yet.') }}</flux:text>
                        @endforelse
                    </div>
                </flux:card>

                <flux:card class="lg:col-span-2">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-200 pb-4 dark:border-zinc-800">
                        <div>
                            <flux:heading size="lg">{{ __('Retirement Timeline') }}</flux:heading>
                            <flux:text>Current retirement age {{ $principalStats['retirementAge'] }} years.</flux:text>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <flux:badge color="red">Retired {{ $principalStats['retired']->count() }} teachers</flux:badge>
                            <flux:badge color="amber">In next 1 yr: {{ $principalStats['upcomingRetirements']->count() }} teachers</flux:badge>
                        </div>
                    </div>

                    @if($principalStats['missingBirthDates'] > 0)
                        <flux:callout class="mt-4" variant="warning">{{ $principalStats['missingBirthDates'] }} teachers are missing birth dates.</flux:callout>
                    @endif

                    <div class="mt-5 grid gap-6 md:grid-cols-2">
                        <div>
                            <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Already Retired') }}</p>
                            @forelse($principalStats['retired'] as $row)
                                <div class="flex justify-between gap-3 border-b border-zinc-100 py-2.5 text-sm dark:border-zinc-800">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $row['name'] }}</span>
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ $row['retirement_date']->format('d M Y') }}</span>
                                </div>
                            @empty
                                <flux:text class="text-sm">{{ __('No retired teachers found.') }}</flux:text>
                            @endforelse
                        </div>
                        <div>
                            <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider">{{ __('Retiring Soon') }}</p>
                            @forelse($principalStats['upcomingRetirements'] as $row)
                                <div class="flex justify-between gap-3 border-b border-zinc-100 py-2.5 text-sm dark:border-zinc-800">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $row['name'] }}</span>
                                    <span class="text-amber-600 dark:text-amber-400">{{ $row['retirement_date']->format('d M Y') }}</span>
                                </div>
                            @empty
                                <flux:text class="text-sm">{{ __('No upcoming retirements found.') }}</flux:text>
                            @endforelse
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- ======================================= -->
            <!-- TEACHER DASHBOARD                       -->
            <!-- ======================================= -->
        @elseif(auth()->user()->hasRole('teacher'))
            @if($teacherStats)

                @cannot('teachers.update')
                    @if($teacherStats['profile']->approval_status !== \App\Enums\ApprovalStatus::Rejected)
                        <flux:callout variant="warning" :heading="__('প্রোফাইল সম্পাদনার অনুমতি নেই')">
                            {{ __('শিক্ষক প্রোফাইল তৈরি ও জমা দেওয়ার পর আপনি নিজে আর এটি সম্পাদনা করতে পারবেন না। কোনো তথ্য পরিবর্তনের প্রয়োজন হলে আপনার কলেজের প্রিন্সিপাল বা কর্তৃপক্ষের সঙ্গে যোগাযোগ করুন।') }}
                        </flux:callout>
                    @endif
                @endcannot

                <!-- Teacher Profile Progress -->
                <flux:card class="overflow-hidden border-indigo-200 bg-linear-to-r from-indigo-50 to-sky-50 dark:border-indigo-500/20 dark:from-indigo-500/10 dark:to-sky-500/5">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="max-w-2xl w-full">
                            <div class="flex items-center gap-3">
                                <div class="rounded-xl bg-indigo-600 p-2.5 text-white shadow-sm dark:bg-indigo-500">
                                    <flux:icon.clipboard-document-check class="size-6" />
                                </div>
                                <div>
                                    <flux:heading size="lg" class="text-indigo-950 dark:text-indigo-100">Your profile {{ $teacherStats['completeness']['percentage'] }}% complete</flux:heading>
                                    <flux:text class="text-indigo-700 dark:text-indigo-300">{{ $teacherStats['completeness']['completed'] }} items completed, {{ $teacherStats['completeness']['total'] - $teacherStats['completeness']['completed'] }} items still need to be added.</flux:text>
                                </div>
                            </div>

                            <div class="mt-5 h-2.5 overflow-hidden rounded-full bg-white ring-1 ring-indigo-200 dark:bg-zinc-900 dark:ring-indigo-500/30" role="progressbar" :aria-label="__('Profile completion progress')" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $teacherStats['completeness']['percentage'] }}">
                                <div class="h-full rounded-full bg-linear-to-r from-indigo-500 to-sky-400 transition-all duration-500" style="width: {{ $teacherStats['completeness']['percentage'] }}%"></div>
                            </div>

                            @if($teacherStats['completeness']['percentage'] < 100)
                                <div class="mt-4 flex flex-wrap gap-2 items-center">
                                    <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('Missing fields:') }}</span>
                                    @foreach($teacherStats['completeness']['missing']->take(6) as $field)
                                        <flux:badge color="amber" size="sm">{{ $field }}</flux:badge>
                                    @endforeach
                                    @if($teacherStats['completeness']['missing']->count() > 6)
                                        <flux:badge color="zinc" size="sm">{{ __(':count more', ['count' => $teacherStats['completeness']['missing']->count() - 6]) }}</flux:badge>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($teacherStats['completeness']['percentage'] < 100 && auth()->user()->can('teachers.update'))
                            <div class="lg:max-w-sm w-full">
                                <flux:callout variant="warning" :heading="__('Action Required')">{{ __('Add the missing details to keep your record up to date.') }}</flux:callout>
                                @if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved)
                                    <flux:button class="mt-3 w-full" variant="primary" :href="route('teachers.edit', $teacherStats['profile'])" icon="pencil-square" wire:navigate>{{ __('Edit Profile') }}</flux:button>
                                @else
                                    <flux:text class="mt-3 text-xs">{{ __('Profile edits are available after approval.') }}</flux:text>
                                @endif
                            </div>
                        @elseif($teacherStats['completeness']['percentage'] === 100)
                            <flux:badge color="green" size="lg" class="shadow-sm">{{ __('Profile Complete') }}</flux:badge>
                        @endif
                    </div>
                </flux:card>

                <!-- Teacher Stats Grid -->
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Profile Approval Status') }}</span>
                            <flux:icon.shield-check class="size-5 text-indigo-500 dark:text-indigo-400" />
                        </div>
                        <div class="mt-4">
                            <flux:badge :color="match($teacherStats['profile']->approval_status) { \App\Enums\ApprovalStatus::Approved => 'green', \App\Enums\ApprovalStatus::Rejected => 'red', default => 'amber' }">
                                {{ match($teacherStats['profile']->approval_status) { \App\Enums\ApprovalStatus::Approved => __('Approved'), \App\Enums\ApprovalStatus::Rejected => __('Rejected'), default => __('Pending Approval') } }}
                            </flux:badge>
                        </div>
                    </div>

                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Retirement Date') }}</span>
                            <flux:icon.calendar-days class="size-5 text-sky-500 dark:text-sky-400" />
                        </div>
                        @if($teacherStats['retirementDate'])
                            <p class="mt-3 text-xl font-bold text-zinc-900 dark:text-white">{{ $teacherStats['retirementDate']->format('d M Y') }}</p>
                            <p class="mt-1 text-xs {{ $teacherStats['isRetired'] ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                                {{ $teacherStats['isRetired'] ? __('Retired') : trans_choice(':count day until retirement|:count days until retirement', $teacherStats['daysUntilRetirement']) }}
                            </p>
                        @else
                            <flux:text class="mt-3 text-sm">{{ __('Birth date is required.') }}</flux:text>
                        @endif
                    </div>

                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('ICT Trained Teachers') }}</span>
                            <flux:icon.academic-cap class="size-5 text-emerald-500 dark:text-emerald-400" />
                        </div>
                        <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($teacherStats['trainings']->count()) }}</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Completed training records') }}</p>
                    </div>

                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Last Profile Update') }}</span>
                            <flux:icon.clock class="size-5 text-violet-500 dark:text-violet-400" />
                        </div>
                        <p class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white">{{ $teacherStats['lastUpdatedAt'] ?? __('Never updated') }}</p>
                    </div>
                </div>

                @if($teacherStats['profile']->approval_status !== \App\Enums\ApprovalStatus::Approved)
                    <flux:callout variant="warning" :heading="__('Approval Required')">
                        {{ __('Your teacher profile must be approved before all services are available.') }}
                    </flux:callout>
                @endif

                @if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Rejected && auth()->user()->can('teachers.create'))
                    <flux:callout variant="danger" :heading="__('প্রোফাইলটি প্রত্যাখ্যাত হয়েছে')">
                        {{ __('প্রয়োজনীয় তথ্য সংশোধন ও সম্পূর্ণ করে প্রোফাইলটি পুনরায় অনুমোদনের জন্য জমা দিন।') }}
                        <flux:button class="mt-4" variant="primary" :href="route('teachers.resubmit', $teacherStats['profile'])" wire:navigate>{{ __('প্রোফাইল সংশোধন ও পুনরায় জমা দিন') }}</flux:button>
                    </flux:callout>
                @endif

                <!-- Teacher Bottom Sections -->
                <div class="grid items-start gap-5 xl:grid-cols-3">
                    <flux:card>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <flux:heading size="lg">{{ __('ICT Training Records') }}</flux:heading>
                                <flux:text class="mt-1">{{ __('Review your completed training courses.') }}</flux:text>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon.academic-cap class="size-6" />
                            </div>
                        </div>
                        <div class="mt-5 grid gap-3">
                            @forelse($teacherStats['trainings'] as $training)
                                <div class="flex flex-col justify-between gap-3 rounded-xl border border-zinc-200 px-4 py-3 sm:flex-row sm:items-center dark:border-zinc-700/60 dark:bg-zinc-800/30">
                                    <div>
                                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $training['name'] }}</p>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $training['institute'] ?: __('Institute not specified') }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($training['year'])
                                            <flux:badge color="emerald">{{ $training['year'] }}</flux:badge>
                                        @endif
                                        @if($training['certificate_url'])
                                            <flux:button size="sm" variant="primary" icon="arrow-down-tray" :href="$training['certificate_url']">{{ __('Certificate') }}</flux:button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <flux:callout variant="warning">{{ __('No training records have been added yet.') }}</flux:callout>
                            @endforelse
                        </div>
                    </flux:card>

                    @if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved)
                        <section aria-label="{{ __('Training Opportunities') }}">
                            <livewire:training.upcoming-trainings :days="30" :compact="true" wire:key="teacher-dashboard-upcoming-trainings" />
                        </section>
                    @endif

                    <flux:card class="flex flex-col">
                        <div class="flex items-center gap-3">
                            <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                <flux:icon.identification class="size-6" />
                            </div>
                            <flux:heading size="lg">{{ __('My Profile Summary') }}</flux:heading>
                        </div>
                        <div class="mt-5 grid gap-3 text-sm divide-y divide-zinc-100 dark:divide-zinc-800">
                            <div class="flex justify-between gap-3 py-2">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('Subject') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $teacherStats['profile']->subject?->name ?: __('Not added') }}</span>
                            </div>
                            <div class="flex justify-between gap-3 py-2">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('Designation') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $teacherStats['profile']->designation?->name ?: __('Not added') }}</span>
                            </div>
                            <div class="flex justify-between gap-3 py-2">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('Retirement Age') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $teacherStats['retirementAge'] }} years</span>
                            </div>
                        </div>
                        @if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved && auth()->user()->can('teachers.view'))
                            <flux:button class="mt-6 w-full" variant="primary" :href="route('teachers.show', $teacherStats['profile'])" wire:navigate>{{ __('View Full Profile') }}</flux:button>
                        @endif
                    </flux:card>
                </div>

            @else
                <!-- No Teacher Profile Yet -->
                <flux:card>
                    <flux:heading size="lg">{{ __('Create Teacher Profile') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('Start by creating your teacher profile and linking it to your college.') }}</flux:text>

                    @cannot('teachers.update')
                        <flux:callout class="mt-4" variant="warning">{{ __('প্রোফাইলটি তৈরি ও জমা দেওয়ার পর আপনি নিজে আর সম্পাদনা করতে পারবেন না। জমা দেওয়ার আগে সব তথ্য ভালোভাবে যাচাই করুন।') }}</flux:callout>
                    @endcannot

                    @can('teachers.create')
                        <flux:button class="mt-4" variant="primary" :href="route('teachers.create')" wire:navigate>{{ __('Create Profile') }}</flux:button>
                    @else
                        <flux:callout class="mt-4" variant="danger">{{ __('শিক্ষক প্রোফাইল তৈরির অনুমতি আপনার নেই। কর্তৃপক্ষের সঙ্গে যোগাযোগ করুন।') }}</flux:callout>
                    @endcan
                </flux:card>
            @endif

            <!-- ======================================= -->
            <!-- ADMIN DASHBOARD                         -->
            <!-- ======================================= -->
        @elseif(auth()->user()->isAdmin())

            <!-- Admin Stats Grid -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"><flux:icon.building-library class="size-6" /></div>
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Institutions') }}</span>
                    </div>
                    <p class="mt-5 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['totalColleges']) }}</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Colleges') }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-xl bg-sky-50 p-2.5 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400"><flux:icon.user-group class="size-6" /></div>
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Teachers') }}</span>
                    </div>
                    <p class="mt-5 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['totalTeachers']) }}</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Teachers') }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"><flux:icon.computer-desktop class="size-6" /></div>
                        <span class="text-xs font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-500">{{ __('Inventory') }}</span>
                    </div>
                    <p class="mt-5 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['totalComputers']) }}</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Computers') }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-xl bg-violet-50 p-2.5 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400"><flux:icon.academic-cap class="size-6" /></div>
                        <span class="text-xs font-medium uppercase tracking-wider text-violet-600 dark:text-violet-500">{{ __('Training') }}</span>
                    </div>
                    <p class="mt-5 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['ictTrainingCoverage'], 1) }}%</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('ICT Training Coverage') }}</p>
                </div>
            </div>

            <!-- Admin Graphical Reports -->
            <div class="grid gap-6 xl:grid-cols-2">

                <!-- Lab Report -->
                <section aria-labelledby="college-report-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <flux:heading id="college-report-heading" size="lg">{{ __('Computer Lab Report') }}</flux:heading>
                            <flux:text class="mt-1">{{ __('Track colleges with and without computer lab facilities.') }}</flux:text>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <flux:button :href="route('lab.summary')" variant="ghost" size="sm" icon-trailing="arrow-up-right" wire:navigate>{{ __('View Details') }}</flux:button>
                        @endif
                    </div>

                    <div class="mt-8 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['labCoverage'], 1) }}%</p>
                            <p class="mt-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Colleges with computer labs') }}</p>
                        </div>
                        <div class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                            {{ number_format($report['collegesWithLab']) }} / {{ number_format($report['totalColleges']) }}
                        </div>
                    </div>

                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-rose-100 dark:bg-rose-950/50">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $report['labCoverage'] }}%"></div>
                    </div>

                    <div class="mt-7 grid grid-cols-2 divide-x divide-zinc-200 rounded-xl bg-zinc-50 py-5 dark:divide-zinc-700/50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-700/50">
                        <div class="px-5">
                            <div class="flex items-center gap-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                <span class="size-2.5 rounded-full bg-emerald-500 shadow-sm"></span>{{ __('With Lab') }}
                            </div>
                            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['collegesWithLab']) }}</p>
                        </div>
                        <div class="px-5">
                            <div class="flex items-center gap-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                <span class="size-2.5 rounded-full bg-rose-500 shadow-sm"></span>{{ __('Without Lab') }}
                            </div>
                            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['collegesWithoutLab']) }}</p>
                        </div>
                    </div>
                </section>

                <!-- Training Report -->
                <section aria-labelledby="training-report-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <flux:heading id="training-report-heading" size="lg">{{ __('ICT Training Report') }}</flux:heading>
                            <flux:text class="mt-1">{{ __('Compare teachers with and without ICT training records.') }}</flux:text>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <flux:button :href="route('ict.summary')" variant="ghost" size="sm" icon-trailing="arrow-up-right" wire:navigate>{{ __('View Details') }}</flux:button>
                        @endif
                    </div>

                    <div class="mt-8 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['ictTrainingCoverage'], 1) }}%</p>
                            <p class="mt-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Teachers with ICT training') }}</p>
                        </div>
                        <div class="rounded-full bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400 dark:ring-sky-500/20">
                            {{ number_format($report['teachersWithIctTraining']) }} / {{ number_format($report['totalTeachers']) }}
                        </div>
                    </div>

                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-amber-100 dark:bg-amber-950/50">
                        <div class="h-full rounded-full bg-sky-500" style="width: {{ $report['ictTrainingCoverage'] }}%"></div>
                    </div>

                    <div class="mt-7 grid grid-cols-2 divide-x divide-zinc-200 rounded-xl bg-zinc-50 py-5 dark:divide-zinc-700/50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-700/50">
                        <div class="px-5">
                            <div class="flex items-center gap-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                <span class="size-2.5 rounded-full bg-sky-500 shadow-sm"></span>{{ __('Trained') }}
                            </div>
                            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithIctTraining']) }}</p>
                        </div>
                        <div class="px-5">
                            <div class="flex items-center gap-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                <span class="size-2.5 rounded-full bg-amber-500 shadow-sm"></span>{{ __('Not Trained') }}
                            </div>
                            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithoutIctTraining']) }}</p>
                        </div>
                    </div>
                </section>

            </div>
        @endif

    </div>
</x-layouts::app>

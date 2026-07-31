<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <header class="relative overflow-hidden rounded-2xl bg-linear-to-br from-indigo-700 via-indigo-600 to-sky-500 px-6 py-7 text-white shadow-lg sm:px-8">
            <div class="absolute -end-16 -top-20 size-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-24 end-28 size-48 rounded-full bg-sky-300/20"></div>
            <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                        <flux:icon.chart-bar-square class="size-4" />
                        {{ auth()->user()->isAdmin() ? 'প্রতিষ্ঠান ও শিক্ষক তথ্যচিত্র' : (auth()->user()->role === \App\Enums\UserRole::Principal ? 'কলেজ পরিচালনা' : 'শিক্ষক সেবা') }}
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ auth()->user()->isAdmin() ? 'এডমিন ড্যাশবোর্ড' : (auth()->user()->role === \App\Enums\UserRole::Principal ? 'প্রিন্সিপাল ড্যাশবোর্ড' : 'শিক্ষক ড্যাশবোর্ড') }}</h1>
                    <p class="mt-2 max-w-2xl text-sm text-indigo-100 sm:text-base">{{ auth()->user()->isAdmin() ? 'সকল কলেজ, শিক্ষক, কম্পিউটার ল্যাব ও ট্রেনিংয়ের সার্বিক অবস্থা দেখুন।' : (auth()->user()->role === \App\Enums\UserRole::Principal ? 'নিজ কলেজের প্রোফাইল, শিক্ষক এবং নিজের শিক্ষক প্রোফাইল পরিচালনা করুন।' : 'নিজের শিক্ষক প্রোফাইল ও অনুমোদনের বর্তমান অবস্থা দেখুন।') }}</p>
                </div>

                @if (auth()->user()->isAdmin() && $report['lastUpdatedAt'])
                    <div class="flex items-center gap-2 text-xs text-indigo-100">
                        <flux:icon.clock class="size-4" />
                        সর্বশেষ তথ্য: {{ $report['lastUpdatedAt'] }}
                    </div>
                @endif
            </div>
        </header>

        @if(auth()->user()->role === \App\Enums\UserRole::Principal && ! auth()->user()->isApproved())
            <flux:card><flux:heading size="lg">Principal account অনুমোদনের অপেক্ষায়</flux:heading><flux:callout class="mt-4" variant="warning" heading="Admin approval প্রয়োজন">আপনার account এবং নির্বাচিত কলেজ Admin যাচাই করছেন। অনুমোদনের পর কলেজ প্রোফাইল সম্পাদনা ও কলেজের শিক্ষক ব্যবস্থাপনা করতে পারবেন।</flux:callout></flux:card>
        @elseif(auth()->user()->role === \App\Enums\UserRole::Principal)
            <div class="grid gap-5 md:grid-cols-3">
                <flux:card class="flex flex-col"><div class="flex items-center gap-3"><div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><flux:icon.identification class="size-6" /></div><flux:heading size="lg">আমার প্রোফাইল</flux:heading></div><flux:text class="mt-3">আপনার শিক্ষক প্রোফাইলের পেশাগত, যোগাযোগ, ব্যাংক ও ট্রেনিংয়ের সকল তথ্য দেখুন।</flux:text>@if(auth()->user()->teacherProfile)<flux:button class="mt-5" variant="primary" :href="route('teachers.show', auth()->user()->teacherProfile)" wire:navigate>সম্পূর্ণ প্রোফাইল দেখুন</flux:button>@else<flux:callout class="mt-5" variant="warning">আপনার শিক্ষক প্রোফাইল সংযুক্ত নেই।</flux:callout>@endif</flux:card>
                <flux:card class="flex flex-col"><div class="flex items-center gap-3"><div class="rounded-lg bg-sky-50 p-2.5 text-sky-600 dark:bg-sky-950 dark:text-sky-300"><flux:icon.building-library class="size-6" /></div><flux:heading size="lg">কলেজ প্রোফাইল</flux:heading></div><flux:text class="mt-3">নিজ কলেজের পরিচিতি, ঠিকানা, প্রোগ্রাম এবং কম্পিউটার ল্যাবের তথ্য দেখুন ও সম্পাদনা করুন।</flux:text><flux:button class="mt-5" :href="route('colleges.show', auth()->user()->college_id)" wire:navigate>কলেজ প্রোফাইল দেখুন</flux:button></flux:card>
                <flux:card class="flex flex-col"><div class="flex items-center gap-3"><div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><flux:icon.user-group class="size-6" /></div><flux:heading size="lg">কলেজের শিক্ষক</flux:heading></div><flux:text class="mt-3">নিজ কলেজের {{ number_format($report['totalTeachers']) }} জন শিক্ষক সার্চ, যাচাই ও অনুমোদন করুন।</flux:text><flux:button class="mt-5" :href="route('teachers.manage')" wire:navigate>শিক্ষক ব্যবস্থাপনা</flux:button></flux:card>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">মোট শিক্ষক</span><flux:icon.user-group class="size-5 text-indigo-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalTeachers']) }}</p></div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">ট্রেনিংপ্রাপ্ত</span><flux:icon.academic-cap class="size-5 text-emerald-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['teachersWithIctTraining']) }}</p><p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">{{ number_format($report['ictTrainingCoverage'], 1) }}% কভারেজ</p></div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">অবসরপ্রাপ্ত</span><flux:icon.user-minus class="size-5 text-rose-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($principalStats['retired']->count()) }}</p></div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-center justify-between"><span class="text-sm text-zinc-500 dark:text-zinc-400">আগামী ১ বছরে অবসর</span><flux:icon.clock class="size-5 text-amber-500" /></div><p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($principalStats['upcomingRetirements']->count()) }}</p></div>
            </div>
            <div class="grid gap-5 lg:grid-cols-2">
                <flux:card><flux:heading size="lg">বিষয়ভিত্তিক শিক্ষক</flux:heading><div class="mt-4 grid gap-3">@forelse($principalStats['subjects'] as $subject)<div class="flex items-center justify-between gap-4 rounded-lg bg-zinc-50 px-4 py-3 dark:bg-zinc-800"><span>{{ $subject->subject }}</span><flux:badge color="indigo">{{ $subject->teachers_count }} জন</flux:badge></div>@empty<flux:text>কোনো বিষয়ভিত্তিক তথ্য নেই।</flux:text>@endforelse</div></flux:card>
                <flux:card><flux:heading size="lg">শিক্ষকদের ট্রেনিং</flux:heading><div class="mt-4 grid gap-3">@forelse($principalStats['trainings'] as $training)<div class="flex items-center justify-between gap-4 rounded-lg bg-zinc-50 px-4 py-3 dark:bg-zinc-800"><span>{{ $training['name'] }}</span><flux:badge color="emerald">{{ $training['count'] }} জন</flux:badge></div>@empty<flux:text>কোনো ট্রেনিং তথ্য নেই।</flux:text>@endforelse</div></flux:card>
                <flux:card class="lg:col-span-2"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><flux:heading size="lg">অবসর পরিস্থিতি</flux:heading><flux:text>বর্তমান নির্ধারিত অবসর বয়স {{ $principalStats['retirementAge'] }} বছর।</flux:text></div><div class="flex gap-2"><flux:badge color="red">অবসরপ্রাপ্ত {{ $principalStats['retired']->count() }} জন</flux:badge><flux:badge color="amber">আগামী ১ বছরে {{ $principalStats['upcomingRetirements']->count() }} জন</flux:badge></div></div>@if($principalStats['missingBirthDates'] > 0)<flux:callout class="mt-4" variant="warning">{{ $principalStats['missingBirthDates'] }} জন শিক্ষকের জন্ম তারিখ যোগ করা হয়নি।</flux:callout>@endif<div class="mt-4 grid gap-4 md:grid-cols-2"><div><p class="mb-2 text-sm font-semibold">ইতোমধ্যে অবসরপ্রাপ্ত</p>@forelse($principalStats['retired'] as $row)<div class="flex justify-between gap-3 border-b border-zinc-200 py-2 text-sm dark:border-zinc-700"><span>{{ $row['name'] }}</span><span>{{ $row['retirement_date']->format('d M Y') }}</span></div>@empty<flux:text>কেউ নেই।</flux:text>@endforelse</div><div><p class="mb-2 text-sm font-semibold">আগামী এক বছরে অবসর</p>@forelse($principalStats['upcomingRetirements'] as $row)<div class="flex justify-between gap-3 border-b border-zinc-200 py-2 text-sm dark:border-zinc-700"><span>{{ $row['name'] }}</span><span>{{ $row['retirement_date']->format('d M Y') }}</span></div>@empty<flux:text>কেউ নেই।</flux:text>@endforelse</div></div></flux:card>
            </div>
        @elseif(auth()->user()->role === \App\Enums\UserRole::Teacher)
            @if($teacherStats)
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">প্রোফাইল অবস্থা</span><flux:icon.shield-check class="size-5 text-indigo-500" /></div>
                        <div class="mt-4"><flux:badge :color="$teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved ? 'green' : 'amber'">{{ $teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved ? 'অনুমোদিত' : 'অনুমোদনের অপেক্ষায়' }}</flux:badge></div>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">অবসরের তারিখ</span><flux:icon.calendar-days class="size-5 text-sky-500" /></div>
                        @if($teacherStats['retirementDate'])<p class="mt-3 text-xl font-bold text-zinc-950 dark:text-white">{{ $teacherStats['retirementDate']->format('d M Y') }}</p><p class="mt-1 text-xs {{ $teacherStats['isRetired'] ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $teacherStats['isRetired'] ? 'আপনার অবসরের বয়স অতিক্রান্ত হয়েছে' : number_format($teacherStats['daysUntilRetirement']) . ' দিন বাকি' }}</p>@else<flux:text class="mt-3">জন্ম তারিখ যোগ করা হয়নি</flux:text>@endif
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">সম্পন্ন ট্রেনিং</span><flux:icon.academic-cap class="size-5 text-emerald-500" /></div>
                        <p class="mt-3 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($teacherStats['trainings']->count()) }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">টি ট্রেনিং রেকর্ড</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm text-zinc-500 dark:text-zinc-400">সর্বশেষ আপডেট</span><flux:icon.clock class="size-5 text-violet-500" /></div>
                        <p class="mt-3 text-sm font-semibold text-zinc-950 dark:text-white">{{ $teacherStats['lastUpdatedAt'] ?? 'তথ্য পাওয়া যায়নি' }}</p>
                    </div>
                </div>

                @if($teacherStats['profile']->approval_status !== \App\Enums\ApprovalStatus::Approved)
                    <flux:callout variant="warning" heading="অনুমোদনের অপেক্ষায়">আপনার প্রোফাইলটি কলেজ প্রিন্সিপালের কাছে পাঠানো হয়েছে। অনুমোদনের পর প্রোফাইল সম্পাদনা করতে পারবেন।</flux:callout>
                @endif

                <div class="grid gap-5 lg:grid-cols-3">
                    <flux:card class="lg:col-span-2">
                        <div class="flex items-start justify-between gap-4"><div><flux:heading size="lg">আমার ট্রেনিং</flux:heading><flux:text class="mt-1">আপনার সম্পন্ন ট্রেনিং, প্রতিষ্ঠান এবং বছরের তালিকা</flux:text></div><div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><flux:icon.academic-cap class="size-6" /></div></div>
                        <div class="mt-5 grid gap-3">@forelse($teacherStats['trainings'] as $training)<div class="flex flex-col justify-between gap-2 rounded-xl border border-zinc-200 px-4 py-3 sm:flex-row sm:items-center dark:border-zinc-700"><div><p class="font-semibold text-zinc-950 dark:text-white">{{ $training['name'] }}</p><p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $training['institute'] ?: 'প্রতিষ্ঠানের তথ্য নেই' }}</p></div>@if($training['year'])<flux:badge color="emerald">{{ $training['year'] }}</flux:badge>@endif</div>@empty<flux:callout variant="warning">আপনার কোনো ট্রেনিং তথ্য যোগ করা হয়নি।</flux:callout>@endforelse</div>
                    </flux:card>
                    <flux:card class="flex flex-col">
                        <div class="flex items-center gap-3"><div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><flux:icon.identification class="size-6" /></div><flux:heading size="lg">আমার প্রোফাইল</flux:heading></div>
                        <div class="mt-4 grid gap-3 text-sm"><div class="flex justify-between gap-3"><span class="text-zinc-500 dark:text-zinc-400">বিষয়</span><span class="font-medium text-zinc-950 dark:text-white">{{ $teacherStats['profile']->subject ?: 'যোগ করা হয়নি' }}</span></div><div class="flex justify-between gap-3"><span class="text-zinc-500 dark:text-zinc-400">পদবি</span><span class="font-medium text-zinc-950 dark:text-white">{{ $teacherStats['profile']->designation ?: 'যোগ করা হয়নি' }}</span></div><div class="flex justify-between gap-3"><span class="text-zinc-500 dark:text-zinc-400">অবসর বয়স</span><span class="font-medium text-zinc-950 dark:text-white">{{ $teacherStats['retirementAge'] }} বছর</span></div></div>
                        @if($teacherStats['profile']->approval_status === \App\Enums\ApprovalStatus::Approved)<flux:button class="mt-6" variant="primary" :href="route('teachers.show', $teacherStats['profile'])" wire:navigate>সম্পূর্ণ প্রোফাইল দেখুন</flux:button>@endif
                    </flux:card>
                </div>
            @else
                <flux:card><flux:heading size="lg">শিক্ষক প্রোফাইল</flux:heading><flux:text class="mt-2">প্রোফাইল তৈরি করে আপনার কলেজ প্রিন্সিপালের অনুমোদনের জন্য জমা দিন।</flux:text><flux:button class="mt-4" variant="primary" :href="route('teachers.create')" wire:navigate>প্রোফাইল তৈরি করুন</flux:button></flux:card>
            @endif
        @elseif(auth()->user()->isAdmin())
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><flux:icon.building-library class="size-6" /></div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">সকল প্রতিষ্ঠান</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalColleges']) }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">মোট কলেজ</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-sky-50 p-2.5 text-sky-600 dark:bg-sky-950 dark:text-sky-300"><flux:icon.user-group class="size-6" /></div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">সকল শিক্ষক</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalTeachers']) }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">মোট শিক্ষক</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><flux:icon.computer-desktop class="size-6" /></div>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">ল্যাবভুক্ত কলেজে</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['totalComputers']) }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">মোট কম্পিউটার</p>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="rounded-lg bg-violet-50 p-2.5 text-violet-600 dark:bg-violet-950 dark:text-violet-300"><flux:icon.academic-cap class="size-6" /></div>
                    <span class="text-xs font-medium text-violet-600 dark:text-violet-400">ট্রেনিং অগ্রগতি</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['ictTrainingCoverage'], 1) }}%</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">আইসিটি ট্রেনিং কভারেজ</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section aria-labelledby="college-report-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading id="college-report-heading" size="lg">কম্পিউটার ল্যাব রিপোর্ট</flux:heading>
                        <flux:text class="mt-1">কলেজগুলোর ল্যাব সুবিধার তুলনামূলক চিত্র</flux:text>
                    </div>
                    @if(auth()->user()->isAdmin())<flux:button :href="route('lab.summary')" variant="ghost" size="sm" icon-trailing="arrow-up-right" wire:navigate>বিস্তারিত</flux:button>@endif
                </div>

                <div class="mt-7 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-4xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['labCoverage'], 1) }}%</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">কলেজে কম্পিউটার ল্যাব আছে</p>
                    </div>
                    <div class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ number_format($report['collegesWithLab']) }} / {{ number_format($report['totalColleges']) }}</div>
                </div>

                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-rose-100 dark:bg-rose-950">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $report['labCoverage'] }}%"></div>
                </div>

                <div class="mt-6 grid grid-cols-2 divide-x divide-zinc-200 rounded-xl bg-zinc-50 py-4 dark:divide-zinc-700 dark:bg-zinc-800/60">
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-emerald-500"></span>ল্যাব আছে</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['collegesWithLab']) }}</p>
                    </div>
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-rose-500"></span>ল্যাব নেই</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['collegesWithoutLab']) }}</p>
                    </div>
                </div>
            </section>

            <section aria-labelledby="training-report-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading id="training-report-heading" size="lg">আইসিটি ট্রেনিং রিপোর্ট</flux:heading>
                        <flux:text class="mt-1">শিক্ষকদের প্রশিক্ষণ অগ্রগতির তুলনামূলক চিত্র</flux:text>
                    </div>
                    @if(auth()->user()->isAdmin())<flux:button :href="route('ict.summary')" variant="ghost" size="sm" icon-trailing="arrow-up-right" wire:navigate>বিস্তারিত</flux:button>@endif
                </div>

                <div class="mt-7 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-4xl font-bold text-zinc-950 dark:text-white">{{ number_format($report['ictTrainingCoverage'], 1) }}%</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">শিক্ষক আইসিটি ট্রেনিং পেয়েছেন</p>
                    </div>
                    <div class="rounded-full bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-700 dark:bg-sky-950 dark:text-sky-300">{{ number_format($report['teachersWithIctTraining']) }} / {{ number_format($report['totalTeachers']) }}</div>
                </div>

                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-amber-100 dark:bg-amber-950">
                    <div class="h-full rounded-full bg-sky-500" style="width: {{ $report['ictTrainingCoverage'] }}%"></div>
                </div>

                <div class="mt-6 grid grid-cols-2 divide-x divide-zinc-200 rounded-xl bg-zinc-50 py-4 dark:divide-zinc-700 dark:bg-zinc-800/60">
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-sky-500"></span>ট্রেনিংপ্রাপ্ত</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithIctTraining']) }}</p>
                    </div>
                    <div class="px-4">
                        <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="size-2.5 rounded-full bg-amber-500"></span>ট্রেনিংবিহীন</div>
                        <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($report['teachersWithoutIctTraining']) }}</p>
                    </div>
                </div>
            </section>
        </div>
        @endif
    </div>
</x-layouts::app>

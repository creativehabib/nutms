<div class="space-y-8">
    <div class="rounded-3xl bg-gradient-to-br from-emerald-800 to-teal-900 px-6 py-10 text-white sm:px-10">
        <a href="{{ route('public.colleges.index') }}" wire:navigate class="text-sm font-semibold text-emerald-200 hover:text-white">← সকল কলেজ</a>
            <div class="mt-5 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-emerald-300">অধিভুক্ত কলেজ</p>
                    <h1 class="mt-2 text-3xl font-extrabold sm:text-4xl">{{ $college->name }}</h1>
                    <p class="mt-3 text-emerald-50/80">কলেজ কোড: {{ $college->college_code ?: 'উল্লেখ নেই' }}</p>
                </div>
                <span class="w-fit rounded-full bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/20">
                    {{ $college->teachers_count }} জন শিক্ষক
                </span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="border-b border-slate-200 bg-slate-50 px-6 py-4 text-lg font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white">কলেজের তথ্য</h2>
                <dl class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach([
                        'কলেজের ধরন' => ['government' => 'সরকারি', 'non_government' => 'বেসরকারি', 'other' => 'অন্যান্য'][$college->college_type] ?? 'উল্লেখ নেই',
                        'অধ্যক্ষ' => $college->principal?->name ?: 'উল্লেখ নেই',
                        'ইমেইল' => $college->college_email ?: 'উল্লেখ নেই',
                        'ওয়েবসাইট' => $college->college_website ?: 'উল্লেখ নেই',
                    ] as $label => $value)
                        <div class="grid gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-semibold text-slate-500">{{ $label }}</dt>
                            <dd class="break-words text-sm text-slate-900 sm:col-span-2 dark:text-slate-100">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="border-b border-slate-200 bg-slate-50 px-6 py-4 text-lg font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white">ঠিকানা ও অবস্থান</h2>
                <dl class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach([
                        'বিভাগ' => $college->division?->bn_name ?: $college->division?->name ?: 'উল্লেখ নেই',
                        'জেলা' => $college->district?->bn_name ?: $college->district?->name ?: 'উল্লেখ নেই',
                        'থানা' => $college->thana?->bn_name ?: $college->thana?->name ?: 'উল্লেখ নেই',
                        'ঠিকানা' => $college->address ?: 'উল্লেখ নেই',
                    ] as $label => $value)
                        <div class="grid gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-semibold text-slate-500">{{ $label }}</dt>
                            <dd class="text-sm text-slate-900 sm:col-span-2 dark:text-slate-100">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/50">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">অধিভুক্ত বিষয় ও কোর্সসমূহ</h2>
            </div>
            <div class="grid gap-5 p-6 md:grid-cols-2">
                @forelse($college->programs as $program)
                    <div class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                        <h3 class="font-bold text-emerald-700 dark:text-emerald-400">
                            {{ ['degree' => 'ডিগ্রি', 'honours' => 'অনার্স', 'masters' => 'মাস্টার্স', 'professional' => 'প্রফেশনাল', 'other' => 'অন্যান্য'][$program->level] ?? $program->level }}
                        </h3>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($program->items ?: [$program->name] as $subject)
                                <span class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $subject }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 md:col-span-2">এই কলেজে এখনো কোনো অধিভুক্ত বিষয় বা কোর্স যোগ করা হয়নি।</p>
                @endforelse
            </div>
    </section>
</div>

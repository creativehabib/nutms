<div class="space-y-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-800 to-teal-700 px-5 py-10 text-white shadow-xl sm:px-10 sm:py-14">
        <div class="absolute -right-16 -top-20 size-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative max-w-3xl">
            <a href="{{ route('home') }}#colleges" wire:navigate class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-200 transition hover:text-white">← হোমে ফিরুন</a>
            <p class="mt-7 text-sm font-bold uppercase tracking-[0.2em] text-emerald-300">জাতীয় বিশ্ববিদ্যালয়</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">অধিভুক্ত কলেজ ডিরেক্টরি</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-emerald-50/80 sm:text-lg">কলেজের অবস্থান, অধিভুক্ত প্রোগ্রাম ও বিষয় এক জায়গায় খুঁজে দেখুন।</p>
        </div>
    </section>

    <section class="sticky top-20 z-20 rounded-2xl border border-slate-200/80 bg-white/95 p-4 shadow-lg shadow-slate-200/50 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/95 dark:shadow-black/20 sm:p-5">
        <div class="grid gap-4 xl:grid-cols-[minmax(280px,1.4fr)_repeat(3,minmax(170px,1fr))]">
            <div>
                <label for="college-search" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">কলেজ বা বিষয় খুঁজুন</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
                    <input id="college-search" type="search" wire:model.live.debounce.350ms="search"
                           placeholder="নাম, কোড অথবা বিষয়"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-emerald-500">
                </div>
            </div>
            <div>
                <label for="college-type-filter" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">কলেজের ধরন</label>
                <select id="college-type-filter" wire:model.live="collegeType"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <option value="">সকল ধরন</option>
                    <option value="government">সরকারি</option>
                    <option value="non_government">বেসরকারি</option>
                    <option value="other">অন্যান্য</option>
                </select>
            </div>
            <div>
                <label for="division-filter" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">বিভাগ</label>
                <select id="division-filter" wire:model.live="division"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <option value="">সকল বিভাগ</option>
                    @foreach($divisions as $divisionOption)
                        <option wire:key="division-option-{{ $divisionOption->id }}" value="{{ $divisionOption->id }}">{{ $divisionOption->bn_name ?: $divisionOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="district-filter" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">জেলা</label>
                <select id="district-filter" wire:model.live="district" @disabled($division === '') data-public-district-filter
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:disabled:bg-slate-900 dark:disabled:text-slate-600">
                    <option value="">{{ $division === '' ? 'আগে বিভাগ নির্বাচন করুন' : 'সকল জেলা' }}</option>
                    @foreach($districts as $districtOption)
                        <option wire:key="district-option-{{ $districtOption->id }}" value="{{ $districtOption->id }}">{{ $districtOption->bn_name ?: $districtOption->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($search !== '' || $collegeType !== '' || $division !== '' || $district !== '')
            <div class="mt-3 flex justify-end">
                <button type="button" wire:click="clearFilters" class="rounded-lg px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">সব ফিল্টার মুছুন</button>
            </div>
        @endif
        <div wire:loading.flex wire:target="search,collegeType,division,district,clearFilters" class="mt-3 items-center gap-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
            <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"/></svg>
            কলেজ খোঁজা হচ্ছে...
        </div>
    </section>

    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">কলেজসমূহ</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $colleges->total() }}টি অনুমোদিত কলেজ পাওয়া গেছে</p>
        </div>
        <p class="text-sm text-slate-500">পৃষ্ঠা {{ $colleges->currentPage() }} / {{ $colleges->lastPage() }}</p>
    </div>

    <div wire:loading.class="opacity-50" wire:target="search,collegeType,division,district,clearFilters" class="grid gap-5 transition-opacity sm:grid-cols-2 xl:grid-cols-3">
        @forelse($colleges as $college)
            @php
                $subjects = $college->programs->flatMap(fn ($program) => $program->items ?: [$program->name])->filter()->unique()->values();
            @endphp
            <article wire:key="college-{{ $college->id }}" class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-900/10 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-800">
                <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
                <div class="flex flex-1 flex-col p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m3 21 18 0M5 21V9m14 12V9M3 9l9-6 9 6M9 21v-6h6v6M8 12h.01M12 12h.01M16 12h.01"/></svg>
                        </div>
                        @if($college->college_code)
                            <span class="max-w-36 truncate rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">কোড: {{ $college->college_code }}</span>
                        @endif
                    </div>

                    <h3 class="mt-5 line-clamp-2 text-lg font-black leading-7 text-slate-900 transition group-hover:text-emerald-700 dark:text-white dark:group-hover:text-emerald-400">{{ $college->name }}</h3>
                    <p class="mt-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2"/></svg>
                        <span class="truncate">{{ $college->district?->bn_name ?: $college->district?->name ?: 'জেলা উল্লেখ নেই' }}{{ $college->division ? ', '.($college->division->bn_name ?: $college->division->name) : '' }}</span>
                    </p>

                    <div class="mt-5 flex-1 border-t border-slate-100 pt-4 dark:border-slate-800">
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">অধিভুক্ত বিষয় / কোর্স</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse($subjects->take(4) as $subject)
                                <span class="max-w-full truncate rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-bold text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $subject }}</span>
                            @empty
                                <span class="text-sm text-slate-400">বিষয়ের তথ্য যোগ করা হয়নি</span>
                            @endforelse
                            @if($subjects->count() > 4)
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">+{{ $subjects->count() - 4 }} আরও</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('public.colleges.show', $college) }}" wire:navigate
                       class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500">
                        কলেজের বিস্তারিত
                        <svg class="size-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center sm:col-span-2 xl:col-span-3 dark:border-slate-700 dark:bg-slate-900">
                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-slate-100 text-3xl dark:bg-slate-800">🔎</div>
                <h3 class="mt-5 text-lg font-black text-slate-900 dark:text-white">কোনো কলেজ পাওয়া যায়নি</h3>
                <p class="mt-2 text-sm text-slate-500">অনুসন্ধান পরিবর্তন করে আবার চেষ্টা করুন।</p>
                <button wire:click="clearFilters" class="mt-5 text-sm font-bold text-emerald-700 hover:underline dark:text-emerald-400">সব কলেজ দেখুন</button>
            </div>
        @endforelse
    </div>

    @if($colleges->hasPages())
        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            {{ $colleges->links() }}
        </div>
    @endif
</div>

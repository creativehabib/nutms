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

    <div data-affiliated-colleges-table wire:loading.class="opacity-50" wire:target="search,collegeType,division,district,clearFilters" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-opacity dark:border-slate-800 dark:bg-slate-900 sm:p-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>কলেজ কোড</flux:table.column>
                <flux:table.column>কলেজের নাম</flux:table.column>
                <flux:table.column>ইমেইল</flux:table.column>
                <flux:table.column>কলেজের ধরন</flux:table.column>
                <flux:table.column class="text-right">অ্যাকশন</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($colleges as $college)
                    <flux:table.row wire:key="college-{{ $college->id }}" class="transition-colors hover:bg-emerald-50/60 dark:hover:bg-emerald-500/5">
                        <flux:table.cell class="whitespace-nowrap font-mono font-semibold text-slate-700 dark:text-slate-300">
                            {{ $college->college_code ?: '—' }}
                        </flux:table.cell>
                        <flux:table.cell class="font-semibold text-slate-900 dark:text-white">
                            {{ $college->name }}
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap text-slate-600 dark:text-slate-300">
                            {{ $college->college_email ?: '—' }}
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            <flux:badge :color="$college->college_type === 'government' ? 'emerald' : 'zinc'" size="sm">
                                {{ ['government' => 'সরকারি', 'non_government' => 'বেসরকারি', 'other' => 'অন্যান্য'][$college->college_type] ?? 'উল্লেখ নেই' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap text-right">
                            <flux:button :href="route('public.colleges.show', $college)" wire:navigate variant="primary" size="sm" icon="eye">
                                দেখুন
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <div class="px-6 py-12 text-center">
                                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-slate-100 text-3xl dark:bg-slate-800">🔎</div>
                                <h3 class="mt-5 text-lg font-black text-slate-900 dark:text-white">কোনো কলেজ পাওয়া যায়নি</h3>
                                <p class="mt-2 text-sm text-slate-500">অনুসন্ধান পরিবর্তন করে আবার চেষ্টা করুন।</p>
                                <button wire:click="clearFilters" class="mt-5 text-sm font-bold text-emerald-700 hover:underline dark:text-emerald-400">সব কলেজ দেখুন</button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    @if($colleges->hasPages())
        <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            {{ $colleges->links() }}
        </div>
    @endif
</div>

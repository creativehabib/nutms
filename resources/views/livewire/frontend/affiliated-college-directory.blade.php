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
                            <flux:button wire:click="viewCollege({{ $college->id }})" wire:loading.attr="disabled" wire:target="viewCollege({{ $college->id }})" variant="primary" size="sm" icon="eye">
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

    <flux:modal wire:model="showCollegeModal" name="affiliated-college-details" @close="$wire.closeCollegeModal()" class="max-w-3xl bg-white text-zinc-900 dark:bg-zinc-900 dark:text-white">
        @if($selectedCollege)
            <div class="space-y-4" data-affiliated-college-modal>
                <div class="flex items-start gap-3">
                    <flux:avatar
                        size="xl"
                        :name="$selectedCollege->college_name_bn ?: $selectedCollege->name"
                        :src="$selectedCollege->logo ? asset('storage/'.$selectedCollege->logo) : null"
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading size="xl">{{ $selectedCollege->college_name_bn ?: $selectedCollege->name }}</flux:heading>
                            <flux:badge color="emerald" size="sm">অধিভুক্ত</flux:badge>
                        </div>
                        @if($selectedCollege->college_name_bn)
                            <flux:text class="mt-1">{{ $selectedCollege->name }}</flux:text>
                        @endif
                        <div class="mt-2 flex flex-wrap gap-2">
                            <flux:badge size="sm">কোড: {{ $selectedCollege->college_code ?: 'উল্লেখ নেই' }}</flux:badge>
                            <flux:badge size="sm">EIIN: {{ $selectedCollege->eiin ?: 'উল্লেখ নেই' }}</flux:badge>
                            <flux:badge size="sm" icon="users">{{ $selectedCollege->teachers_count }} জন শিক্ষক</flux:badge>
                        </div>
                    </div>
                </div>

                @if($selectedCollege->about)
                    @php($collegeAbout = trim($selectedCollege->about))
                    <flux:callout
                        wire:key="college-about-{{ $selectedCollege->id }}"
                        x-data="{ expanded: false }"
                        variant="info"
                        icon="information-circle"
                        heading="কলেজ পরিচিতি"
                    >
                        <flux:text class="leading-6" x-bind:class="{ 'line-clamp-3': ! expanded }">
                            {{ $collegeAbout }}
                        </flux:text>
                        @if(mb_strlen($collegeAbout) > 180)
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="mt-1"
                                x-on:click="expanded = ! expanded"
                                x-bind:aria-expanded="expanded"
                                x-text="expanded ? 'কম দেখুন' : 'আরও দেখুন'"
                                data-expand-college-about
                            />
                        @endif
                    </flux:callout>
                @endif

                <div class="grid gap-3 md:grid-cols-2">
                    <flux:card class="space-y-3 bg-white p-4 dark:bg-zinc-800">
                        <flux:heading>কলেজের তথ্য</flux:heading>
                        <flux:separator variant="subtle" />
                        <dl class="space-y-2.5">
                            @foreach([
                                    'কলেজের ধরন' => ['government' => 'সরকারি', 'non_government' => 'বেসরকারি', 'other' => 'অন্যান্য'][$selectedCollege->college_type] ?? 'উল্লেখ নেই',
                                    'শিক্ষার্থীভিত্তিক ধরন' => ['B' => 'বয়েজ এন্ড গার্লস মিশ্র কলেজ', 'F' => 'শুধু গার্লস কলেজ'][$selectedCollege->male_female] ?? 'উল্লেখ নেই',
                                    'মোট জমি' => $selectedCollege->total_land ?: 'উল্লেখ নেই',
                                    'অধ্যক্ষ' => $selectedCollege->principal?->name ?: 'উল্লেখ নেই',
                                    'প্রতিষ্ঠার বছর' => $selectedCollege->establish_year ?: 'উল্লেখ নেই',
                                    'ফোন' => $selectedCollege->college_phone ?: 'উল্লেখ নেই',
                                    'ইমেইল' => $selectedCollege->college_email ?: 'উল্লেখ নেই',
                                    'ওয়েবসাইট' => $selectedCollege->college_website ?: 'উল্লেখ নেই',
                                ] as $label => $value)
                                <div class="grid grid-cols-[7rem_1fr] gap-2">
                                    <dt><flux:text size="sm">{{ $label }}</flux:text></dt>
                                    <dd><flux:text size="sm" class="break-words font-medium text-zinc-900 dark:text-white">{{ $value }}</flux:text></dd>
                                </div>
                            @endforeach
                        </dl>
                    </flux:card>

                    <flux:card class="space-y-3 bg-white p-4 dark:bg-zinc-800">
                        <flux:heading>ঠিকানা ও অবস্থান</flux:heading>
                        <flux:separator variant="subtle" />
                        <dl class="space-y-2.5">
                            @foreach([
                                    'বিভাগ' => $selectedCollege->division?->bn_name ?: $selectedCollege->division?->name ?: 'উল্লেখ নেই',
                                    'জেলা' => $selectedCollege->district?->bn_name ?: $selectedCollege->district?->name ?: 'উল্লেখ নেই',
                                    'থানা' => $selectedCollege->thana?->bn_name ?: $selectedCollege->thana?->name ?: 'উল্লেখ নেই',
                                    'ঠিকানা' => $selectedCollege->address ?: 'উল্লেখ নেই',
                                ] as $label => $value)
                                <div class="grid grid-cols-[4rem_1fr] gap-2">
                                    <dt><flux:text size="sm">{{ $label }}</flux:text></dt>
                                    <dd><flux:text size="sm" class="break-words font-medium text-zinc-900 dark:text-white">{{ $value }}</flux:text></dd>
                                </div>
                            @endforeach
                        </dl>
                    </flux:card>
                </div>

                <flux:card class="space-y-3 bg-white p-4 dark:bg-zinc-800">
                    <flux:heading>অধিভুক্ত বিষয় ও কোর্সসমূহ</flux:heading>
                    <flux:separator variant="subtle" />
                    <div class="grid gap-3 sm:grid-cols-2">
                        @forelse($selectedCollege->programs as $program)
                            <flux:card class="space-y-2 bg-zinc-50 p-3 dark:bg-zinc-900">
                                <flux:heading size="sm">{{ $programLevelNames->get($program->level, $program->level) }}</flux:heading>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($program->items ?: [$program->name] as $subject)
                                        <flux:badge color="emerald" size="sm">{{ $subject }}</flux:badge>
                                    @endforeach
                                </div>
                            </flux:card>
                        @empty
                            <flux:text class="sm:col-span-2">এই কলেজে এখনো কোনো অধিভুক্ত বিষয় বা কোর্স যোগ করা হয়নি।</flux:text>
                        @endforelse
                    </div>
                </flux:card>

                <div class="flex justify-end">
                    <flux:modal.close>
                        <flux:button type="button" variant="primary" wire:click="closeCollegeModal">বন্ধ করুন</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>

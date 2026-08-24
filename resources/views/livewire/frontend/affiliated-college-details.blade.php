<div class="space-y-8">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-800 to-teal-900 px-6 py-10 text-white sm:px-10">
        @if($college->banner)
            <img src="{{ asset('storage/'.$college->banner) }}" alt="{{ $college->name }}" class="absolute inset-0 h-full w-full object-cover opacity-20">
        @endif
        <div class="relative">
        <a href="{{ route('public.colleges.index') }}" wire:navigate class="text-sm font-semibold text-emerald-200 hover:text-white">← সকল কলেজ</a>
            <div class="mt-5 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div class="flex items-center gap-5">
                    @if($college->logo)
                        <img src="{{ asset('storage/'.$college->logo) }}" alt="{{ $college->name }} লোগো" class="size-24 rounded-2xl bg-white object-contain p-2 shadow-lg">
                    @endif
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wider text-emerald-300">অধিভুক্ত কলেজ</p>
                        <h1 class="mt-2 text-3xl font-extrabold sm:text-4xl">{{ $college->college_name_bn ?: $college->name }}</h1>
                        @if($college->college_name_bn)<p class="mt-1 text-emerald-50/80">{{ $college->name }}</p>@endif
                        <p class="mt-3 text-emerald-50/80">কলেজ কোড: {{ $college->college_code ?: 'উল্লেখ নেই' }} · EIIN: {{ $college->eiin ?: 'উল্লেখ নেই' }}</p>
                    </div>
                </div>
                <span class="w-fit rounded-full bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/20">
                    {{ $college->teachers_count }} জন শিক্ষক
                </span>
            </div>
        </div>
    </div>

        @if($college->about)
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">কলেজ পরিচিতি</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $college->about }}</p>
            </section>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="border-b border-slate-200 bg-slate-50 px-6 py-4 text-lg font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white">কলেজের তথ্য</h2>
                <dl class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach([
                        'কলেজের ধরন' => ['government' => 'সরকারি', 'non_government' => 'বেসরকারি', 'other' => 'অন্যান্য'][$college->college_type] ?? 'উল্লেখ নেই',
                        'অধ্যক্ষ' => $college->principal?->name ?: 'উল্লেখ নেই',
                        'প্রতিষ্ঠার বছর' => $college->establish_year ?: 'উল্লেখ নেই',
                        'শিক্ষার্থীভিত্তিক ধরন' => ['B' => 'বয়েজ এন্ড গার্লস মিশ্র কলেজ', 'F' => 'শুধু গার্লস কলেজ'][$college->male_female] ?? 'উল্লেখ নেই',
                        'মোট জমি' => $college->total_land ?: 'উল্লেখ নেই',
                        'ফোন' => $college->college_phone ?: 'উল্লেখ নেই',
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
            <h2 class="border-b border-slate-200 bg-slate-50 px-6 py-4 text-lg font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white">কম্পিউটার ল্যাব</h2>
            <div class="grid gap-4 p-6 sm:grid-cols-3">
                <div><p class="text-xs font-semibold text-slate-500">ল্যাব আছে?</p><p class="mt-1 font-bold">{{ $college->has_computer_lab === null ? 'উল্লেখ নেই' : ($college->has_computer_lab ? 'হ্যাঁ' : 'না') }}</p></div>
                @if($college->has_computer_lab)
                    <div><p class="text-xs font-semibold text-slate-500">ডেস্কটপ</p><p class="mt-1 font-bold">{{ $college->desktop_count ?? 0 }}</p></div>
                    <div><p class="text-xs font-semibold text-slate-500">ল্যাপটপ</p><p class="mt-1 font-bold">{{ $college->laptop_count ?? 0 }}</p></div>
                @endif
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/50">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">অধিভুক্ত বিষয় ও কোর্সসমূহ</h2>
            </div>
            <div class="grid gap-5 p-6 md:grid-cols-2">
                @forelse($college->programs as $program)
                    <div class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                        <h3 class="font-bold text-emerald-700 dark:text-emerald-400">
                            {{ $programLevelNames->get($program->level, $program->level) }}
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

    @if($relatedColleges->isNotEmpty())
        <section
            class="space-y-4"
            aria-labelledby="related-colleges-heading"
            data-related-colleges
            data-related-colleges-carousel
            x-data="{
                scrollPage(direction) {
                    this.$refs.track.scrollBy({ left: direction * this.$refs.track.clientWidth, behavior: 'smooth' });
                }
            }"
        >
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 id="related-colleges-heading" class="text-2xl font-black text-slate-900 dark:text-white">এই অঞ্চলের আরও কলেজ</h2>
                    <p class="mt-1 text-sm text-slate-500">একই বিভাগে থাকা অন্যান্য অনুমোদিত কলেজ প্রোফাইল দেখুন।</p>
                </div>

                @if($relatedColleges->count() > 1)
                    <div class="flex shrink-0 gap-2">
                        <flux:button type="button" variant="outline" size="sm" icon="chevron-left" x-on:click="scrollPage(-1)" aria-label="{{ __('Previous colleges') }}" />
                        <flux:button type="button" variant="outline" size="sm" icon="chevron-right" x-on:click="scrollPage(1)" aria-label="{{ __('Next colleges') }}" />
                    </div>
                @endif
            </div>

            <div x-ref="track" class="-mx-2 flex snap-x snap-mandatory overflow-x-auto scroll-smooth pb-2">
                @foreach($relatedColleges as $relatedCollege)
                    <div wire:key="related-college-{{ $relatedCollege->id }}" class="w-full shrink-0 snap-start px-2 sm:w-1/2 lg:w-1/4">
                        <a
                            href="{{ $relatedCollege->publicProfileUrl() }}"
                            wire:navigate
                            class="group block h-full rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700"
                        >
                            <div class="flex items-start gap-3">
                                <flux:avatar
                                    size="lg"
                                    :name="$relatedCollege->college_name_bn ?: $relatedCollege->name"
                                    :src="$relatedCollege->logo ? asset('storage/'.$relatedCollege->logo) : null"
                                />
                                <div class="min-w-0">
                                    <h3 class="font-bold leading-6 text-slate-900 group-hover:text-emerald-700 dark:text-white dark:group-hover:text-emerald-400">
                                        {{ $relatedCollege->college_name_bn ?: $relatedCollege->name }}
                                    </h3>
                                    @if($relatedCollege->college_name_bn)
                                        <p class="mt-1 text-xs text-slate-500">{{ $relatedCollege->name }}</p>
                                    @endif
                                    <p class="mt-2 text-xs font-semibold text-slate-500">কলেজ কোড: {{ $relatedCollege->college_code ?: 'উল্লেখ নেই' }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

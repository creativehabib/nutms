<x-layouts::app.welcome title="অধিভুক্ত কলেজসমূহ">
    <div class="space-y-8">
        <div class="rounded-3xl bg-gradient-to-br from-emerald-800 to-teal-900 px-6 py-10 text-white sm:px-10">
            <a href="{{ route('home') }}#colleges" class="text-sm font-semibold text-emerald-200 hover:text-white">← হোমে ফিরুন</a>
            <h1 class="mt-4 text-3xl font-extrabold sm:text-4xl">অধিভুক্ত কলেজসমূহ</h1>
            <p class="mt-3 max-w-2xl leading-7 text-emerald-50/80">কলেজের নাম, কোড অথবা অধিভুক্ত বিষয় দিয়ে খুঁজুন।</p>

            <form action="{{ route('public.colleges.index') }}" method="GET" class="mt-7 flex max-w-2xl flex-col gap-3 sm:flex-row">
                <label for="college-search" class="sr-only">কলেজ খুঁজুন</label>
                <input id="college-search" name="search" value="{{ $search }}" type="search"
                       placeholder="কলেজ, কোড বা বিষয় লিখুন"
                       class="min-w-0 flex-1 rounded-xl border-0 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none ring-2 ring-transparent placeholder:text-slate-400 focus:ring-emerald-300">
                <button class="rounded-xl bg-emerald-500 px-6 py-3 font-bold text-white transition hover:bg-emerald-400">খুঁজুন</button>
            </form>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($colleges as $college)
                @php
                    $subjects = $college->programs
                        ->flatMap(fn ($program) => $program->items ?: [$program->name])
                        ->filter()
                        ->unique()
                        ->values();
                @endphp
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex size-12 items-center justify-center rounded-xl bg-emerald-50 text-2xl dark:bg-emerald-500/10">🏛️</div>
                        @if($college->college_code)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $college->college_code }}</span>
                        @endif
                    </div>
                    <h2 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">{{ $college->name }}</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        {{ $college->district?->bn_name ?: $college->district?->name ?: 'জেলা উল্লেখ নেই' }}
                    </p>
                    <div class="mt-5 flex flex-1 flex-wrap content-start gap-2">
                        @forelse($subjects->take(6) as $subject)
                            <span class="h-fit rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300">{{ $subject }}</span>
                        @empty
                            <span class="text-sm text-slate-400">কোনো বিষয় যোগ করা হয়নি</span>
                        @endforelse
                    </div>
                    <a href="{{ $college->publicProfileUrl() }}"
                       class="mt-6 inline-flex justify-center rounded-xl theme-primary-bg px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">
                        বিস্তারিত দেখুন
                    </a>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500 md:col-span-2 lg:col-span-3 dark:border-slate-700 dark:bg-slate-900">
                    আপনার অনুসন্ধানে কোনো অধিভুক্ত কলেজ পাওয়া যায়নি।
                </div>
            @endforelse
        </div>

        {{ $colleges->links() }}
    </div>
</x-layouts::app.welcome>

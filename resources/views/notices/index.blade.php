<x-layouts::app.welcome
    title="জাতীয় বিশ্ববিদ্যালয়ের সকল নোটিশ"
    description="জাতীয় বিশ্ববিদ্যালয়ের সাম্প্রতিক নোটিশ খুঁজুন এবং দেখুন।"
    keywords="জাতীয় বিশ্ববিদ্যালয় নোটিশ, NU notice, examination notice"
>
    @php
        $toBengaliNumber = fn (string|int $value): string => strtr((string) $value, [
            '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪',
            '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯',
        ]);
    @endphp

    <section data-notice-archive class="mx-auto max-w-7xl" aria-labelledby="notice-archive-title">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
            <div class="min-w-0">
                <div class="overflow-hidden rounded-3xl border border-sky-200 bg-white shadow-sm dark:border-sky-900 dark:bg-slate-900">
                    <div class="grid sm:grid-cols-[1fr_180px]">
                        <div class="bg-gradient-to-r from-sky-700 to-cyan-600 p-6 text-white sm:p-7">
                            <div class="flex items-center gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl border border-white/40 bg-white/10">
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0m6 0H9"/></svg>
                                </div>
                                <div>
                                    <h1 id="notice-archive-title" class="text-2xl font-extrabold">সাম্প্রতিক সংবাদ ও নোটিশ</h1>
                                    <p class="mt-1 text-sm text-sky-100">জাতীয় বিশ্ববিদ্যালয়ের নোটিশ ও সংবাদ আর্কাইভ</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-2 bg-sky-50 p-5 font-bold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                            প্রকাশের তারিখ
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('notices.index') }}" data-notice-search class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <label class="relative flex-1">
                            <span class="sr-only">নোটিশের শিরোনাম খুঁজুন</span>
                            <svg class="absolute top-1/2 left-4 size-5 -translate-y-1/2 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.5-4.5m2-5A7 7 0 114.5 11.5a7 7 0 0114 0z"/></svg>
                            <input name="search" value="{{ $search }}" type="search" placeholder="নোটিশের শিরোনাম লিখে খুঁজুন..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-12 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 dark:border-slate-700 dark:bg-slate-950">
                        </label>
                        @if($category !== '')
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        <button type="submit" class="rounded-xl bg-sky-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-sky-800">অনুসন্ধান করুন</button>
                        @if($search !== '' || $category !== '')
                            <a href="{{ route('notices.index') }}" wire:navigate class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">মুছুন</a>
                        @endif
                    </div>
                    <p class="mt-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        মোট {{ $toBengaliNumber($notices->total()) }}টি ফলাফল
                        @if($notices->total() > 0)
                            · {{ $toBengaliNumber($notices->firstItem()) }}–{{ $toBengaliNumber($notices->lastItem()) }} দেখানো হচ্ছে
                        @endif
                    </p>
                </form>

                <div data-notice-list class="mt-5 grid gap-3">
                    @forelse($notices as $notice)
                        <article class="overflow-hidden rounded-2xl border border-slate-200 border-l-4 border-l-sky-600 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:border-l-sky-500 dark:bg-slate-900">
                            <div class="grid sm:grid-cols-[1fr_180px_70px] sm:items-stretch">
                                <div class="p-5">
                                    <h2 class="font-bold leading-7 text-slate-800 dark:text-slate-100">{{ $notice['title'] }}</h2>
                                    @if($notice['category'])
                                        <span class="mt-3 inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700 dark:bg-sky-950 dark:text-sky-300">{{ $notice['category'] }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 border-t border-slate-100 bg-slate-50 px-5 py-3 text-sm font-bold text-sky-700 dark:border-slate-800 dark:bg-slate-950/50 dark:text-sky-300 sm:border-t-0 sm:border-l">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                                    {{ $notice['published_at'] ? $toBengaliNumber($notice['published_at']) : 'তারিখ নেই' }}
                                </div>
                                <div class="flex items-center justify-center border-t border-slate-100 p-3 dark:border-slate-800 sm:border-t-0 sm:border-l">
                                    @if($notice['url'])
                                        <a href="{{ $notice['url'] }}" target="_blank" rel="noopener noreferrer" class="flex size-11 items-center justify-center rounded-full bg-violet-700 text-white shadow-lg shadow-violet-700/20 transition hover:scale-105" aria-label="নোটিশটি খুলুন">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 3h7v7m0-7L10 14M5 7v12h12v-5"/></svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">লিংক নেই</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-700 dark:bg-slate-900">
                            <div class="text-4xl">🔍</div>
                            <h2 class="mt-4 font-extrabold text-slate-800 dark:text-white">কোনো নোটিশ পাওয়া যায়নি</h2>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">অন্য শব্দ দিয়ে খুঁজুন অথবা অনুসন্ধান মুছে আবার চেষ্টা করুন।</p>
                        </div>
                    @endforelse
                </div>

                @if($notices->hasPages())
                    <div data-notice-pagination class="mt-6">{{ $notices->onEachSide(1)->links() }}</div>
                @endif
            </div>

            <aside class="grid content-start gap-5">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="bg-sky-700 px-5 py-3 text-lg font-extrabold text-white">নোটিশ বিভাগ</h2>
                    <nav class="divide-y divide-slate-100 dark:divide-slate-800" aria-label="নোটিশ বিভাগ">
                        <a href="{{ route('notices.index') }}" wire:navigate class="flex items-center justify-between gap-3 px-5 py-3 text-sm font-semibold transition hover:bg-sky-50 hover:text-sky-700 dark:hover:bg-sky-950/30">
                            <span>সকল সাম্প্রতিক নোটিশ</span><span>{{ $toBengaliNumber($totalNotices) }}</span>
                        </a>
                        @foreach($categories as $categoryName => $count)
                            <a href="{{ route('notices.index', ['category' => $categoryName]) }}" wire:navigate class="flex items-center justify-between gap-3 px-5 py-3 text-sm transition hover:bg-sky-50 hover:text-sky-700 dark:hover:bg-sky-950/30 {{ $category === $categoryName ? 'bg-sky-50 font-bold text-sky-700 dark:bg-sky-950/30 dark:text-sky-300' : '' }}">
                                <span class="min-w-0 truncate">{{ $categoryName }}</span><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs dark:bg-slate-800">{{ $toBengaliNumber($count) }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h2 class="bg-sky-700 px-5 py-3 text-lg font-extrabold text-white">প্রয়োজনীয় লিংক</h2>
                    <div class="grid divide-y divide-slate-100 text-sm dark:divide-slate-800">
                        <a href="https://www.nu.ac.bd/" target="_blank" rel="noopener noreferrer" class="px-5 py-3 font-semibold hover:bg-sky-50 hover:text-sky-700 dark:hover:bg-sky-950/30">জাতীয় বিশ্ববিদ্যালয়ের ওয়েবসাইট ↗</a>
                        <a href="https://results.nu.ac.bd/" target="_blank" rel="noopener noreferrer" class="px-5 py-3 font-semibold hover:bg-sky-50 hover:text-sky-700 dark:hover:bg-sky-950/30">পরীক্ষার ফলাফল ↗</a>
                        <a href="{{ route('tools.index') }}" wire:navigate class="px-5 py-3 font-semibold hover:bg-sky-50 hover:text-sky-700 dark:hover:bg-sky-950/30">অনলাইন টুলস</a>
                        <a href="{{ route('public.colleges.index') }}" wire:navigate class="px-5 py-3 font-semibold hover:bg-sky-50 hover:text-sky-700 dark:hover:bg-sky-950/30">অধিভুক্ত কলেজসমূহ</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-xs leading-6 text-amber-900 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200">
                    নোটিশের তথ্য বাহ্যিক উৎস থেকে স্বয়ংক্রিয়ভাবে সংগ্রহ করা হয়। চূড়ান্ত তথ্যের জন্য সংযুক্ত মূল নোটিশটি যাচাই করুন।
                </div>
            </aside>
        </div>
    </section>
</x-layouts::app.welcome>

<header
    x-data="{
        mobileMenuOpen: false,
        toggleColorMode() {
            this.$flux.appearance = this.$flux.dark ? 'light' : 'dark';
        },
    }"
    class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" wire:navigate class="flex min-w-0 items-center gap-3">
            <div class="theme-primary-bg flex size-12 shrink-0 items-center justify-center rounded-xl text-white shadow">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12v5c4 3 10 3 14 0v-5"/>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="theme-primary-text block truncate text-lg font-bold leading-tight">জাতীয় বিশ্ববিদ্যালয়</span>
                <span class="block truncate text-xs text-slate-500 dark:text-slate-400">শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা</span>
            </div>
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="প্রধান নেভিগেশন">
            <a href="{{ route('home') }}#home" class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">হোম</a>
            <a href="{{ route('home') }}#about" class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">আমাদের সম্পর্কে</a>
            <a href="{{ route('home') }}#services" class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">সেবাসমূহ</a>
            <a href="{{ route('public.colleges.index') }}" wire:navigate class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">কলেজসমূহ</a>
            <a href="{{ route('home') }}#training" class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">প্রশিক্ষণ</a>
            <a href="{{ route('home') }}#notices" class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">নোটিশ</a>
            <a href="{{ route('home') }}#contact" class="text-sm font-medium text-slate-700 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">যোগাযোগ</a>
        </nav>

        <div class="flex items-center gap-2">
            <button
                type="button"
                data-theme-toggle
                @click="toggleColorMode()"
                class="inline-flex size-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-slate-700 dark:bg-slate-900 dark:text-amber-300 dark:hover:bg-slate-800 dark:hover:text-amber-200"
                :aria-label="$flux.dark ? 'লাইট মোড চালু করুন' : 'ডার্ক মোড চালু করুন'"
                :aria-pressed="$flux.dark"
                :title="$flux.dark ? 'লাইট মোড' : 'ডার্ক মোড'"
            >
                <svg x-show="! $flux.dark" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
                <svg x-show="$flux.dark" x-cloak class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.05 16.95l-1.41 1.41m12.72 0-1.42-1.42M7.05 7.05 5.64 5.64M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
               class="theme-primary-bg theme-primary-hover hidden items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition sm:inline-flex">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m-5-4l5-5m0 0l-5-5m5 5H3"/>
                </svg>
                {{ auth()->check() ? 'ড্যাশবোর্ড' : 'লগইন' }}
            </a>
            <button type="button" @click="mobileMenuOpen = ! mobileMenuOpen"
                    class="inline-flex size-11 items-center justify-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 lg:hidden"
                    aria-label="মেনু খুলুন" :aria-expanded="mobileMenuOpen">
                <svg x-show="! mobileMenuOpen" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>

    <nav x-show="mobileMenuOpen" x-cloak x-transition @click.outside="mobileMenuOpen = false"
         class="border-t border-slate-200 bg-white px-4 py-4 shadow-xl dark:border-slate-800 dark:bg-slate-950 lg:hidden"
         aria-label="মোবাইল নেভিগেশন">
        <div class="mx-auto grid max-w-7xl gap-1 sm:grid-cols-2">
            @foreach([
                [route('home').'#home', 'হোম'],
                [route('home').'#about', 'আমাদের সম্পর্কে'],
                [route('home').'#services', 'সেবাসমূহ'],
                [route('public.colleges.index'), 'কলেজসমূহ'],
                [route('home').'#training', 'প্রশিক্ষণ'],
                [route('home').'#notices', 'নোটিশ'],
                [route('home').'#contact', 'যোগাযোগ'],
            ] as [$url, $label])
                <a href="{{ $url }}" @click="mobileMenuOpen = false"
                   class="rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-200 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400">
                    {{ $label }}
                </a>
            @endforeach
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
               class="theme-primary-bg mt-2 rounded-lg px-4 py-3 text-center text-sm font-bold text-white sm:hidden">
                {{ auth()->check() ? 'ড্যাশবোর্ড' : 'লগইন' }}
            </a>
        </div>
    </nav>
</header>

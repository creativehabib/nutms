<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => __('National University Teacher Training Department')])

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body x-data="{ scrolled: false, mobileMenuOpen: false }"
      @scroll.window="scrolled = (window.pageYOffset > 20)"
      class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased transition-colors duration-300 flex flex-col">

<div class="relative isolate overflow-hidden flex-1 flex flex-col">

    <!-- Dynamic Background Effects -->
    <div class="absolute inset-0 -z-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300"></div>
    <div class="absolute top-0 left-0 w-full h-[600px] -z-10 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-emerald-200/40 via-slate-50 to-transparent dark:from-emerald-900/20 dark:via-slate-950 dark:to-transparent opacity-80 blur-[80px] pointer-events-none transition-colors duration-300"></div>

    <!-- ========================================== -->
    <!-- Dynamic Sticky Header -->
    <!-- ========================================== -->
    <header :class="scrolled ? 'bg-white/85 dark:bg-slate-950/85 border-b border-slate-200/50 dark:border-slate-800/50 shadow-sm backdrop-blur-xl' : 'bg-transparent border-transparent'"
            class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">

            <div class="flex items-center gap-8">
                <!-- Logo & Brand Name -->
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 group" aria-label="{{ __('Home') }}">
                        <span class="flex size-10 sm:size-11 shrink-0 items-center justify-center rounded-xl border border-emerald-200 bg-white text-emerald-600 shadow-md shadow-emerald-200/40 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400 dark:shadow-emerald-950/50 transition-transform duration-300 group-hover:scale-105 group-hover:shadow-lg">
                            <svg class="size-6 sm:size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m3 9 9-5 9 5-9-5Zm3 3v5c3.5 2.7 8.5 2.7 12 0v-5m3-3v6"></path></svg>
                        </span>
                    <span class="min-w-0">
                            <span class="block truncate text-sm sm:text-base font-bold text-slate-800 dark:text-white transition-colors tracking-tight">{{ __('National University') }}</span>
                            <span class="block truncate text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 transition-colors uppercase tracking-wider font-semibold">{{ __('Teacher Training Dept.') }}</span>
                        </span>
                </a>

                <!-- Desktop Primary Navigation (Hover Dropdown) -->
                <nav class="hidden md:flex items-center gap-6 h-full" aria-label="{{ __('Primary navigation') }}">

                    <!-- Hover Dropdown Wrapper -->
                    <div x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false" class="relative h-12 flex items-center">
                        <button class="text-sm font-semibold text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5 h-full">
                            {{ __('Surveys & Forms') }}
                            <svg :class="dropdownOpen ? 'rotate-180 text-indigo-600 dark:text-indigo-400' : 'text-slate-400'" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Body -->
                        <div x-show="dropdownOpen" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                             class="absolute top-full left-0 w-[300px] p-2 bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-700/70 rounded-2xl shadow-2xl z-50 before:absolute before:-top-2 before:left-6 before:border-8 before:border-transparent before:border-b-white dark:before:border-b-slate-900">

                            <div class="flex flex-col">
                                <a href="{{ route('survey.student') }}" class="group flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ __('Student Survey') }}</p>
                                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ __('Provide your feedback on ICT courses and learning experience.') }}</p>
                                    </div>
                                </a>

                                <div class="h-px bg-slate-100 dark:bg-slate-800 my-1 mx-2"></div>

                                <a href="{{ route('survey.teacher') }}" class="group flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ __('Teacher Survey') }}</p>
                                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ __('Share insights on teaching methods and professional development.') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-2 sm:gap-4">

                <!-- Dark Mode Toggle Button -->
                <button id="theme-toggle" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none rounded-full p-2.5 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700" aria-label="Toggle Dark Mode">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.32a1 1 0 011.415 0l.708.708a1 1 0 01-1.414 1.415l-.708-.708a1 1 0 010-1.415zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-2.32 4.22a1 1 0 010 1.415l-.708.708a1 1 0 01-1.414-1.415l.708-.708a1 1 0 011.415 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-2.32a1 1 0 01-1.415 0l-.708-.708a1 1 0 011.414-1.415l.708.708a1 1 0 010 1.415zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm2.32-4.22a1 1 0 010-1.415l.708-.708a1 1 0 011.414 1.415l-.708.708a1 1 0 01-1.415 0zM10 5a5 5 0 100 10 5 5 0 000-10z"></path></svg>
                </button>

                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>

                <!-- Auth Nav (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <flux:button :href="route('dashboard')" variant="primary" icon="squares-2x2" class="shadow-sm">{{ __('Dashboard') }}</flux:button>
                    @else
                        <flux:button :href="route('login')" variant="primary" icon="arrow-right-end-on-rectangle" class="shadow-sm">{{ __('Login') }}</flux:button>
                    @endauth
                </div>

                <!-- Hamburger Button (Mobile Only) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 dark:border-slate-700 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 focus:outline-none transition-colors">
                    <span class="sr-only">Open main menu</span>
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- Mobile Dropdown Menu -->
    <!-- ========================================== -->
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.away="mobileMenuOpen = false"
         class="md:hidden fixed top-[72px] inset-x-0 border-b border-slate-200/80 dark:border-slate-800 bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl shadow-2xl z-40">

        <div class="px-4 py-6 space-y-4 max-h-[calc(100vh-80px)] overflow-y-auto">
            <p class="px-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Surveys & Forms') }}</p>

            <div class="grid gap-2">
                <a href="{{ route('survey.student') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 hover:bg-indigo-50 dark:bg-slate-900/50 dark:hover:bg-slate-800/80 transition-colors">
                    <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-slate-800 dark:text-white">{{ __('Student Survey') }}</p>
                    </div>
                </a>

                <a href="{{ route('survey.teacher') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 hover:bg-emerald-50 dark:bg-slate-900/50 dark:hover:bg-slate-800/80 transition-colors">
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-slate-800 dark:text-white">{{ __('Teacher Survey') }}</p>
                    </div>
                </a>
            </div>

            <div class="mt-4 pt-6 border-t border-slate-100 dark:border-slate-800/80">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white font-semibold transition-colors shadow-lg shadow-slate-900/20 dark:shadow-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        {{ __('Go to Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white font-semibold transition-colors shadow-lg shadow-slate-900/20 dark:shadow-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        {{ __('Secure Login') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 w-full mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 sm:py-28">
        {{ $slot }}
    </main>

    <!-- Footer Section -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/20 backdrop-blur-sm transition-colors duration-300">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-500 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ now()->year }} <span class="font-semibold text-slate-700 dark:text-slate-300">{{ __('National University') }}</span>. {{ __('All rights reserved.') }}</p>
            <p class="text-[13px] sm:text-sm font-medium">{{ __('Teacher Management & Training Portal') }}</p>
        </div>
    </footer>

</div>

@persist('toast')
<flux:toast.group>
    <flux:toast />
</flux:toast.group>
@endpersist

@fluxScripts

<!-- Dark Mode Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            window.dispatchEvent(new Event('theme-changed'));
        });
    });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => __('National University Teacher Training Department')])

    <!-- Theme Init Script (Strictly prevents screen flash & reload mismatch) -->
    <script>
        (function() {
            try {
                let theme = localStorage.getItem('theme') || 'system';
                let isSystemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (theme === 'dark' || (theme === 'system' && isSystemDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{
          scrolled: false,
          mobileMenuOpen: false,
          theme: localStorage.getItem('theme') || 'system',

          init() {
              // Watch for system theme changes if set to 'system'
              window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                  if (this.theme === 'system') {
                      if (e.matches) {
                          document.documentElement.classList.add('dark');
                      } else {
                          document.documentElement.classList.remove('dark');
                      }
                  }
              });
          },

          setTheme(val) {
              this.theme = val;
              localStorage.setItem('theme', val);

              if (val === 'dark') {
                  document.documentElement.classList.add('dark');
              } else if (val === 'light') {
                  document.documentElement.classList.remove('dark');
              } else {
                  // system fallback
                  if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                      document.documentElement.classList.add('dark');
                  } else {
                      document.documentElement.classList.remove('dark');
                  }
              }
              window.dispatchEvent(new Event('theme-changed'));
          }
      }"
      @scroll.window="scrolled = (window.pageYOffset > 20)"
      class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased transition-colors duration-300 flex flex-col">

<div class="relative isolate overflow-hidden flex-1 flex flex-col">

    <!-- ========================================== -->
    <!-- Dynamic Background Effects (Light & Dark) -->
    <!-- ========================================== -->
    <!-- Dark Mode Background -->
    <div class="absolute inset-0 -z-20 hidden dark:block bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_35%),radial-gradient(circle_at_85%_25%,_rgba(59,130,246,0.15),_transparent_30%),linear-gradient(to_bottom,_#07111f,_#0f172a)] transition-colors duration-300"></div>
    <!-- Light Mode Background -->
    <div class="absolute inset-0 -z-20 dark:hidden bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.1),_transparent_35%),radial-gradient(circle_at_85%_25%,_rgba(59,130,246,0.1),_transparent_30%),linear-gradient(to_bottom,_#f8fafc,_#f1f5f9)] transition-colors duration-300"></div>

    <div class="absolute inset-x-0 top-0 -z-10 h-px bg-gradient-to-r from-transparent via-emerald-400/50 dark:via-emerald-300/70 to-transparent"></div>

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

                <!-- Desktop Primary Navigation -->
                <nav class="hidden md:flex items-center gap-6 h-full" aria-label="{{ __('Primary navigation') }}">
                    <div x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false" class="relative h-12 flex items-center">
                        <button class="text-sm font-semibold text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5 h-full">
                            {{ __('Surveys & Forms') }}
                            <svg :class="dropdownOpen ? 'rotate-180 text-indigo-600 dark:text-indigo-400' : 'text-slate-400'" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown -->
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
                                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ __('Provide your feedback on ICT courses.') }}</p>
                                    </div>
                                </a>
                                <div class="h-px bg-slate-100 dark:bg-slate-800 my-1 mx-2"></div>
                                <a href="{{ route('survey.teacher') }}" class="group flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ __('Teacher Survey') }}</p>
                                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ __('Share insights on teaching methods.') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-2 sm:gap-4">

                <!-- Flux UI Theme Toggle Dropdown -->
                <flux:dropdown align="end">
                    <flux:button variant="ghost" square class="rounded-full text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" aria-label="Toggle Theme">
                        <flux:icon.sun x-show="theme === 'light'" class="size-5" />
                        <flux:icon.moon x-show="theme === 'dark'" x-cloak class="size-5" />
                        <flux:icon.computer-desktop x-show="theme === 'system'" x-cloak class="size-5" />
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item icon="sun" @click="setTheme('light')" x-bind:class="theme === 'light' ? 'bg-slate-100 dark:bg-slate-800 font-semibold' : ''">{{ __('Light') }}</flux:menu.item>
                        <flux:menu.item icon="moon" @click="setTheme('dark')" x-bind:class="theme === 'dark' ? 'bg-slate-100 dark:bg-slate-800 font-semibold' : ''">{{ __('Dark') }}</flux:menu.item>
                        <flux:menu.item icon="computer-desktop" @click="setTheme('system')" x-bind:class="theme === 'system' ? 'bg-slate-100 dark:bg-slate-800 font-semibold' : ''">{{ __('System') }}</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <flux:button :href="route('dashboard')" variant="primary" icon="squares-2x2" class="shadow-sm">{{ __('Dashboard') }}</flux:button>
                    @else
                        <flux:button :href="route('login')" variant="primary" icon="arrow-right-end-on-rectangle" class="shadow-sm">{{ __('Login') }}</flux:button>
                    @endauth
                </div>

                <!-- Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 dark:border-slate-700 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 focus:outline-none transition-colors">
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
                    </div>
                    <p class="text-base font-bold text-slate-800 dark:text-white">{{ __('Student Survey') }}</p>
                </a>
                <a href="{{ route('survey.teacher') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 hover:bg-emerald-50 dark:bg-slate-900/50 dark:hover:bg-slate-800/80 transition-colors">
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path></svg>
                    </div>
                    <p class="text-base font-bold text-slate-800 dark:text-white">{{ __('Teacher Survey') }}</p>
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

    <!-- ========================================== -->
    <!-- Main Content (Hero Section) -->
    <!-- ========================================== -->
    <main class="flex-1">
        <section class="mx-auto grid min-h-[calc(100vh-5.5rem)] w-full max-w-7xl items-center gap-12 px-4 py-24 sm:px-6 lg:grid-cols-[1.08fr_0.92fr] lg:px-8 lg:py-28">

            <!-- Left Side Content -->
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-300/20 dark:bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold dark:text-emerald-200 sm:text-sm transition-colors duration-300">
                    <span class="size-2 rounded-full bg-emerald-500 dark:bg-emerald-300 shadow-[0_0_12px_rgba(16,185,129,0.5)] dark:shadow-[0_0_12px_rgba(110,231,183,0.9)]"></span>
                    {{ __('Integrated teacher information management platform') }}
                </div>

                <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight tracking-tight text-slate-900 dark:text-white sm:text-5xl lg:text-6xl transition-colors duration-300">
                    {{ __('Teacher development and training management is now') }}
                    <span class="bg-gradient-to-r from-emerald-600 to-cyan-600 dark:from-emerald-300 dark:to-cyan-300 bg-clip-text text-transparent">{{ __('easier and more effective') }}</span>
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600 dark:text-slate-300 sm:text-lg transition-colors duration-300">
                    {{ __('A modern digital system for securely storing, updating, and analyzing teacher information, ICT training, and computer lab data for National University affiliated colleges.') }}
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @auth
                        <flux:button :href="route('teachers.manage')" variant="primary" icon-trailing="arrow-right" class="shadow-lg shadow-indigo-500/20">{{ __('Open teacher management') }}</flux:button>
                    @else
                        <flux:button :href="route('login')" variant="primary" icon-trailing="arrow-right" class="shadow-lg shadow-indigo-500/20">{{ __('Enter management system') }}</flux:button>
                    @endauth
                    <flux:button href="#features" variant="ghost" class="bg-white/50 dark:bg-transparent border-slate-200 dark:border-transparent">{{ __('View system benefits') }}</flux:button>
                </div>

                <!-- Quick Stats Cards -->
                <div class="mt-10 grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:shadow-none dark:border-white/10 dark:bg-white/5 p-4 backdrop-blur transition-colors duration-300">
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-300">Excel</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('Fast teacher data import') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:shadow-none dark:border-white/10 dark:bg-white/5 p-4 backdrop-blur transition-colors duration-300">
                        <p class="text-2xl font-black text-cyan-600 dark:text-cyan-300">Live</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('Search, filters, and reports') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:shadow-none dark:border-white/10 dark:bg-white/5 p-4 backdrop-blur transition-colors duration-300">
                        <p class="text-2xl font-black text-blue-600 dark:text-blue-300">Safe</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ __('Trash and data restore') }}</p>
                    </div>
                </div>
            </div>

            <!-- Right Side Floating Card -->
            <div class="relative mx-auto w-full max-w-xl">
                <div class="absolute -inset-8 -z-10 rounded-full bg-emerald-200/50 dark:bg-emerald-400/10 blur-3xl transition-colors duration-300"></div>

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white/70 shadow-xl shadow-slate-200/50 dark:border-white/15 dark:bg-white/5 dark:shadow-2xl dark:shadow-black/40 backdrop-blur-xl p-3 sm:p-4 transition-colors duration-300">
                    <div class="rounded-2xl border border-slate-100 bg-white dark:border-white/10 dark:bg-slate-900/85 p-5 sm:p-7 transition-colors duration-300">

                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 dark:border-white/10 pb-5 transition-colors duration-300">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-300">Training Office</p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ __('Information Management Center') }}</h2>
                            </div>
                            <span class="flex size-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-400/10 dark:text-blue-300">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87m-2-12a4 4 0 0 1 0 7.75"></path></svg>
                                </span>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-white/8 dark:bg-white/5 transition-colors duration-300">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
                                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7 9 18l-5-5"></path></svg>
                                    </span>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ __('Central teacher database') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('College-wise information storage and updates') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-white/8 dark:bg-white/5 transition-colors duration-300">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600 dark:bg-cyan-400/10 dark:text-cyan-300">
                                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V9m5 10V5m5 14v-7m5 7V3"></path></svg>
                                    </span>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ __('Training progress monitoring') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('ICT and other training summary') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-white/8 dark:bg-white/5 transition-colors duration-300">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-400/10 dark:text-blue-300">
                                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 10h10M9 14h6M11 18h2"></path></svg>
                                    </span>
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ __('Lab facility analysis') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('College computer and lab information overview') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- Features Section -->
        <!-- ========================================== -->
        <section id="features" class="border-y border-slate-200 bg-white/50 dark:border-white/10 dark:bg-white/3 transition-colors duration-300">
            <div class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-300">{{ __('Digital support for office activities') }}</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-900 dark:text-white sm:text-4xl">{{ __('All essential features in one platform') }}</h2>
                    <p class="mt-4 leading-7 text-slate-600 dark:text-slate-400">{{ __('Make teacher training planning and institutional decisions more effective with accurate data.') }}</p>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md dark:shadow-none dark:border-white/10 dark:bg-slate-900/70 p-6 transition-all duration-300">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0-12 4 4m-4-4L8 7M5 21h14a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2"></path></svg>
                            </span>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">{{ __('Easy data import') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ __('Quickly and accurately add college-wise teacher information from Excel and CSV files.') }}</p>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md dark:shadow-none dark:border-white/10 dark:bg-slate-900/70 p-6 transition-all duration-300">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 dark:bg-cyan-400/10 dark:text-cyan-300">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 7.5 7.5M21 3l-7.5 7.5M3 21l7.5-7.5M21 21l-7.5-7.5"></path></svg>
                            </span>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">{{ __('Fast search and filters') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ __('Find the information you need by name, subject, college, and lab facility.') }}</p>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md dark:shadow-none dark:border-white/10 dark:bg-slate-900/70 p-6 transition-all duration-300">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-400/10 dark:text-blue-300">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"></path></svg>
                            </span>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">{{ __('Secure information management') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">{{ __('Manage information confidently with edit, bulk action, soft delete, and restore features.') }}</p>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <!-- ========================================== -->
    <!-- Footer -->
    <!-- ========================================== -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/20 backdrop-blur-sm transition-colors duration-300">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-600 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ now()->year }} <span class="font-semibold text-slate-800 dark:text-slate-300">{{ __('National University') }}</span>. {{ __('All rights reserved.') }}</p>
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
</body>
</html>

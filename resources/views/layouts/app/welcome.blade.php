@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'frontendTheme' => [
        'mode' => 'system',
        'primary_light' => '#047857',
        'primary_dark' => '#34d399',
        'accent_light' => '#0f766e',
        'accent_dark' => '#5eead4',
    ],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" style="--theme-primary-light: {{ $frontendTheme['primary_light'] }}; --theme-primary-dark: {{ $frontendTheme['primary_dark'] }}; --theme-accent-light: {{ $frontendTheme['accent_light'] }}; --theme-accent-dark: {{ $frontendTheme['accent_dark'] }}">
<head>
    @include('partials.head', ['title' => $title ?? __('National University Teacher Training Department')])

    <script>
        const savedTheme = localStorage.getItem('color-theme');
        const defaultTheme = @js($frontendTheme['mode']);
        const useDarkTheme = savedTheme === 'dark'
            || (!savedTheme && defaultTheme === 'dark')
            || (!savedTheme && defaultTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (useDarkTheme) {
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
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased transition-colors duration-300 flex flex-col">

<div class="relative isolate flex-1 flex flex-col">

    <!-- Dynamic Background Effects -->
    <div class="absolute inset-0 -z-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300"></div>
    <div class="absolute top-0 left-0 w-full h-[600px] -z-10 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-emerald-200/40 via-slate-50 to-transparent dark:from-emerald-900/20 dark:via-slate-950 dark:to-transparent opacity-80 blur-[80px] pointer-events-none transition-colors duration-300"></div>

    @include('partials.frontend-navbar')

    <!-- Main Content -->
    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
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

<livewire:ai-chat wire:key="public-ai-chat" />

@persist('toast')
<flux:toast.group>
    <flux:toast />
</flux:toast.group>
@endpersist

@fluxScripts


</body>
</html>

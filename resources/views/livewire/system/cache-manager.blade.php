<div class="mx-auto w-full max-w-5xl space-y-8 p-4 sm:p-6 lg:p-8">

    <!-- ========================================== -->
    <!-- CACHE MANAGEMENT SECTION                   -->
    <!-- ========================================== -->
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2 text-zinc-900 dark:text-white">
            <flux:icon.arrow-path class="size-5 font-bold" />
            <flux:heading size="lg" class="font-bold">{{ __('Cache Management') }}</flux:heading>
        </div>
        <flux:text class="text-sm">{{ __('Clear cache to make your site up to date.') }}</flux:text>
    </div>

    <flux:card class="p-0 overflow-hidden shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <!-- Table Header -->
        <div class="grid grid-cols-[auto_1fr_auto] gap-4 bg-zinc-50/80 px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-500 border-b border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-800">
            <div class="w-14">{{ __('Type') }}</div>
            <div>{{ __('Description') }}</div>
            <div class="text-right">{{ __('Action') }}</div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">

            <!-- Item 1: CMS Cache -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400">
                    <flux:icon.server-stack class="size-6" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Clear all CMS cache') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Clear CMS caching: database caching, static blocks... Run this command when you don\'t see the changes after updating data.') }}</p>

                    <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                        <span class="flex size-2 rounded-full bg-blue-500"></span>
                        {{ __('Current Size:') }} {{ $cacheSize }}
                    </div>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="clearCmsCache" wire:loading.attr="disabled" icon="trash" class="!bg-blue-600 hover:!bg-blue-700 !text-white !border-none shadow-sm">{{ __('Clear') }}</flux:button>
                </div>
            </div>

            <!-- Item 2: Compiled Views -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400">
                    <flux:icon.code-bracket class="size-6" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Refresh compiled views') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Clear compiled views to make views up to date.') }}</p>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="refreshViews" wire:loading.attr="disabled" icon="arrow-path" class="!bg-amber-500 hover:!bg-amber-600 !text-white !border-none shadow-sm">{{ __('Refresh') }}</flux:button>
                </div>
            </div>

            <!-- Item 3: Config Cache -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 border border-sky-100 dark:bg-sky-500/10 dark:border-sky-500/20 dark:text-sky-400">
                    <flux:icon.cog-6-tooth class="size-6" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Clear config cache') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('You might need to refresh the config caching when you change something on production environment.') }}</p>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="clearConfig" wire:loading.attr="disabled" icon="arrow-path" class="!bg-sky-500 hover:!bg-sky-600 !text-white !border-none shadow-sm">{{ __('Clear') }}</flux:button>
                </div>
            </div>

            <!-- Item 4: Route Cache -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400">
                    <flux:icon.adjustments-horizontal class="size-6" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Clear route cache') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Clear cache routing.') }}</p>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="clearRoute" wire:loading.attr="disabled" icon="arrow-path" class="!bg-emerald-500 hover:!bg-emerald-600 !text-white !border-none shadow-sm">{{ __('Clear') }}</flux:button>
                </div>
            </div>

            <!-- Item 5: Clear Log -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400">
                    <flux:icon.document-text class="size-6" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Clear log') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Clear system log files') }}</p>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="clearLogs" wire:loading.attr="disabled" icon="trash" class="!bg-rose-500 hover:!bg-rose-600 !text-white !border-none shadow-sm">{{ __('Clear') }}</flux:button>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="border-t border-zinc-100 bg-zinc-50/50 px-6 py-4 dark:border-zinc-800/80 dark:bg-zinc-800/30">
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <flux:icon.information-circle class="size-5 text-sky-500" />
                {{ __('Clear cache after making changes to your site to ensure they appear correctly.') }}
            </div>
        </div>
    </flux:card>

    <!-- ========================================== -->
    <!-- PERFORMANCE OPTIMIZATION SECTION           -->
    <!-- ========================================== -->
    <div class="flex flex-col gap-2 pt-6">
        <div class="flex items-center gap-2 text-zinc-900 dark:text-white">
            <flux:icon.rocket-launch class="size-5 font-bold" />
            <flux:heading size="lg" class="font-bold">{{ __('Performance Optimization') }}</flux:heading>
        </div>
    </div>

    <flux:card class="p-0 overflow-hidden shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <!-- Table Header -->
        <div class="grid grid-cols-[auto_1fr_auto] gap-4 bg-zinc-50/80 px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-500 border-b border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-800">
            <div class="w-14">{{ __('Type') }}</div>
            <div>{{ __('Description') }}</div>
            <div class="text-right">{{ __('Action') }}</div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">

            <!-- Item 1: Optimize -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400">
                    <flux:icon.bolt class="size-6" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Optimize site performance') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Cache configuration, routes, and views for faster loading speed.') }}</p>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="optimizeSystem" wire:loading.attr="disabled" icon="rocket-launch" class="!bg-emerald-500 hover:!bg-emerald-600 !text-white !border-none shadow-sm">{{ __('Optimize') }}</flux:button>
                </div>
            </div>

            <!-- Item 2: Clear Optimization -->
            <div class="flex items-start gap-4 px-6 py-5 hover:bg-zinc-50/50 transition-colors dark:hover:bg-zinc-800/30">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400">
                    <flux:icon.archive-box-x-mark class="size-6" />
                </div>
                <div class="flex-1 pt-1">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Clear optimization cache') }}</h3>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Remove optimization caches to allow configuration changes.') }}</p>
                </div>
                <div class="shrink-0 pt-1">
                    <flux:button wire:click="clearOptimization" wire:loading.attr="disabled" icon="trash" class="!bg-amber-500 hover:!bg-amber-600 !text-white !border-none shadow-sm">{{ __('Clear') }}</flux:button>
                </div>
            </div>

        </div>
    </flux:card>
</div>

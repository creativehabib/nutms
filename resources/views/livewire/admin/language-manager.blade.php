<div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- ======================================= -->
    <!-- STATS CARDS SECTION                     -->
    <!-- ======================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Total Keys Card -->
        <flux:card class="flex items-center gap-4 py-5 shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                <flux:icon.key class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Keys') }}</flux:heading>
                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalKeys }} <span class="text-sm font-normal text-zinc-500">{{ __('items') }}</span></div>
            </div>
        </flux:card>

        <!-- Translated Card -->
        <flux:card class="flex items-center gap-4 py-5 shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <flux:icon.check-circle class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Translated') }}</flux:heading>
                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $translatedKeys }} <span class="text-sm font-normal text-zinc-500">{{ __('items') }}</span></div>
            </div>
        </flux:card>

        <!-- Missing Translation Card -->
        <flux:card class="flex items-center gap-4 py-5 shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="rounded-xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                <flux:icon.exclamation-circle class="size-6" />
            </div>
            <div>
                <flux:heading size="sm" class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Missing Translation') }}</flux:heading>
                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $missingKeys }} <span class="text-sm font-normal text-zinc-500">{{ __('items') }}</span></div>
            </div>
        </flux:card>
    </div>

    <!-- ======================================= -->
    <!-- TOOLBAR SECTION                         -->
    <!-- ======================================= -->
    <flux:card class="shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">

            <!-- Search Bar -->
            <div class="w-full md:w-1/2 lg:w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search translations...')" class="w-full" />
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <flux:button variant="outline" icon="arrow-path" wire:click="scanCodebase" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="scanCodebase">{{ __('Scan Codebase') }}</span>
                    <span wire:loading wire:target="scanCodebase">{{ __('Scanning...') }}</span>
                </flux:button>

                <flux:button variant="outline" icon="language" wire:click="autoTranslate" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="autoTranslate">{{ __('Auto Translate') }}</span>
                    <span wire:loading wire:target="autoTranslate">{{ __('Translating...') }}</span>
                </flux:button>

                <div class="w-40">
                    <flux:select wire:model.live="locale">
                        <flux:select.option value="bn">{{ __('BN - Bengali') }}</flux:select.option>
                        <flux:select.option value="en">{{ __('EN - English') }}</flux:select.option>
                    </flux:select>
                </div>

                <flux:modal.trigger name="add-translation-modal">
                    <flux:button variant="primary" icon="plus">{{ __('Add New') }}</flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </flux:card>

    <!-- ======================================= -->
    <!-- TRANSLATION TABLE                       -->
    <!-- ======================================= -->
    <flux:card class="p-0 overflow-hidden shadow-sm border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-800 dark:bg-zinc-800/40 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    <th class="px-5 py-3 w-1/4">{{ __('Key') }}</th>
                    <th class="px-5 py-3 w-1/3">{{ __('EN (English)') }}</th>
                    <th class="px-5 py-3 w-1/3">{{ strtoupper($locale) }} {{ __('(Translation)') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Action') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                @forelse($filteredTranslations as $key => $value)
                    <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/30 group">
                        <!-- Key Column -->
                        <td class="px-5 py-3 text-sm font-mono text-zinc-600 dark:text-zinc-400 break-all">
                            {{ $key }}
                        </td>

                        <!-- English Column -->
                        <td class="px-5 py-3 text-sm">
                                <span class="cursor-pointer font-medium text-indigo-600 dark:text-indigo-400 border-b border-dashed border-indigo-400/40 hover:border-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-300 dark:hover:border-indigo-300 transition-colors" wire:click="editKey('{{ $key }}')">
                                    {{ !empty($baseTranslations[$key]) ? $baseTranslations[$key] : __('Empty') }}
                                </span>
                        </td>

                        <!-- Translation Column -->
                        <td class="px-5 py-3 text-sm">
                                <span class="cursor-pointer border-b border-dashed transition-colors {{ empty($value) ? 'text-amber-600 border-amber-400/40 hover:text-amber-700 dark:text-amber-500 dark:hover:text-amber-400 font-medium' : 'font-medium text-indigo-600 dark:text-indigo-400 border-indigo-400/40 hover:border-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-300 dark:hover:border-indigo-300' }}" wire:click="editKey('{{ $key }}')">
                                    {{ !empty($value) ? $value : __('Missing (Empty)') }}
                                </span>
                        </td>

                        <!-- Action Column -->
                        <td class="px-5 py-3 text-right">
                            <flux:button size="sm" variant="subtle" icon="trash" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 opacity-0 group-hover:opacity-100 transition-opacity" wire:click="deleteTranslation('{{ $key }}')" wire:confirm="{{ __('Are you sure you want to delete this key?') }}" aria-label="Delete key" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-500 dark:text-zinc-400">
                                <flux:icon.magnifying-glass class="size-8 mb-3 opacity-20" />
                                <p>{{ __('No translations found.') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    <!-- ======================================= -->
    <!-- EDIT MODAL                              -->
    <!-- ======================================= -->
    <flux:modal name="edit-translation-modal" class="md:w-1/3">
        <form wire:submit="saveSingleKey" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Quick Edit') }}</flux:heading>
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 break-all">
                    {{ __('Key:') }} <span class="font-mono bg-zinc-100 dark:bg-zinc-800/80 px-1.5 py-0.5 rounded text-zinc-700 dark:text-zinc-300">{{ $editingKey }}</span>
                </p>
            </div>

            <flux:separator />

            <div class="space-y-4">
                <flux:textarea wire:model="editEnValue" :label="__('English (EN)')" rows="2" />
                <flux:textarea wire:model="editTargetValue" :label="__('Translation') . ' (' . strtoupper($locale) . ')'" rows="2" />
            </div>

            <div class="flex justify-end gap-3 mt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="check">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- ======================================= -->
    <!-- ADD NEW MODAL                           -->
    <!-- ======================================= -->
    <flux:modal name="add-translation-modal" class="md:w-1/2">
        <form wire:submit="addTranslation" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Add New Translation') }}</flux:heading>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Add a new key-value pair to your language files.') }}</p>
            </div>

            <flux:separator />

            <div class="space-y-4">
                <flux:input wire:model="newKey" :label="__('Key (English Text)')" :placeholder="__('e.g. Total Users')" required />
                <flux:textarea wire:model="newValue" :label="__('Translation') . ' (' . strtoupper($locale) . ')'" :placeholder="__('e.g. Translated Text')" rows="2" required />
            </div>

            <div class="flex justify-end gap-3 mt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" icon="plus">{{ __('Add Translation') }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>

<div class="mx-auto w-full max-w-6xl space-y-6 p-4 sm:p-6">

    <!-- Main Card Container -->
    <flux:card class="p-0 sm:p-0 overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">

        <!-- Header & Search Section -->
        <div class="border-b border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700/50 dark:bg-zinc-900/40 sm:p-6">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 shadow-sm border border-indigo-100 dark:border-indigo-500/10">
                        <flux:icon.server-stack class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $title }} ব্যবস্থাপনা</flux:heading>
                        <flux:text class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">মোট {{ $records->total() }}টি তথ্য যুক্ত আছে</flux:text>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full sm:w-auto">
                    <flux:input wire:model.live.debounce.300ms="search" type="search" placeholder="খুঁজুন..." icon="magnifying-glass" class="w-full sm:w-64 shadow-sm" />
                    <flux:button variant="primary" icon="plus" wire:click="openCreateModal" class="w-full sm:w-auto shadow-sm">
                        নতুন যুক্ত করুন
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto px-4 pb-4 sm:px-6 pt-2">
            <flux:table>
                <flux:table.columns>
                    @if ($isCollege)
                        <flux:table.column>কোড</flux:table.column>
                    @endif
                    <flux:table.column>নাম</flux:table.column>
                    <flux:table.column>ব্যবহৃত</flux:table.column>
                    <flux:table.column>অবস্থা</flux:table.column>
                    <flux:table.column class="text-right">অ্যাকশন</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($records as $record)
                        <flux:table.row wire:key="reference-{{ $type }}-{{ $record->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-200">

                            @if ($isCollege)
                                <flux:table.cell>
                                    <span class="font-mono text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $record->code ?: '—' }}</span>
                                </flux:table.cell>
                            @endif

                            <flux:table.cell class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $record->name }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-300">
                                    <flux:icon.users variant="micro" class="text-zinc-400" />
                                    <span>{{ $record->teachers_count }} জন শিক্ষক</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" :color="$record->is_active ? 'emerald' : 'zinc'">
                                    {{ $record->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $record->id }})" title="সম্পাদনা" class="text-zinc-500 hover:text-indigo-600 dark:hover:text-indigo-400" />
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete({{ $record->id }})" wire:confirm="আপনি কি নিশ্চিত যে এই তথ্যটি মুছে ফেলতে চান?" title="মুছুন" class="text-zinc-500 hover:text-red-600 dark:hover:text-red-400" />
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="{{ $isCollege ? 5 : 4 }}">
                                <div class="flex flex-col items-center justify-center py-12 text-zinc-500 dark:text-zinc-400">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 mb-3">
                                        <flux:icon.document-text class="h-6 w-6 text-zinc-400" />
                                    </div>
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">কোনো তথ্য পাওয়া যায়নি</p>
                                    <p class="text-sm mt-1">নতুন তথ্য যোগ করুন অথবা অন্য কি-ওয়ার্ড দিয়ে খুঁজুন।</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        @if($records->hasPages())
            <div class="border-t border-zinc-200 p-4 px-4 sm:px-6 dark:border-zinc-700/50 bg-zinc-50/30 dark:bg-zinc-900/30">
                {{ $records->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Modern Modal for Add/Edit -->
    <flux:modal name="reference-data-form" wire:model="showModal" @close="cancelEdit" focusable class="max-w-md p-0 overflow-hidden">
        <form wire:submit="save">

            <!-- Modal Header -->
            <div class="flex items-center gap-3 border-b border-zinc-100 bg-zinc-50/80 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                    <flux:icon.pencil-square class="size-4" />
                </div>
                <div>
                    <flux:heading size="lg" class="font-semibold">{{ $editingId ? $title.' সম্পাদনা' : 'নতুন '.$title }}</flux:heading>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 bg-white dark:bg-zinc-900">
                @if ($isCollege)
                    <div class="space-y-1">
                        <flux:input wire:model="code" label="কলেজ কোড" placeholder="যেমন: ১০১০" />
                        @error('code') <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="space-y-1">
                    <flux:input wire:model="name" :label="$title.' নাম'" placeholder="সঠিক নাম লিখুন" required />
                    @error('name') <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <flux:switch wire:model="isActive" label="সক্রিয় রাখুন" description="নিষ্ক্রিয় করলে এটি ডাটা এন্ট্রির সময় ড্রপডাউনে দেখাবে না।" />
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 border-t border-zinc-100 bg-zinc-50/50 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <flux:button type="button" variant="ghost" wire:click="cancelEdit" class="w-full sm:w-auto">বাতিল করুন</flux:button>
                <flux:button type="submit" variant="primary" icon="check-circle" wire:loading.attr="disabled" class="w-full sm:w-auto shadow-sm">
                    {{ $editingId ? 'আপডেট করুন' : 'সংরক্ষণ করুন' }}
                </flux:button>
            </div>

        </form>
    </flux:modal>
</div>

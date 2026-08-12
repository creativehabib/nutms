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
                        <flux:heading size="lg" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $title }} Management</flux:heading>
                        <flux:text class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ trans_choice(':count record added|:count records added', $records->total()) }}</flux:text>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full sm:w-auto">
                    <flux:input wire:model.live.debounce.300ms="search" type="search" :placeholder="__('Search...')" icon="magnifying-glass" class="w-full sm:w-64 shadow-sm" />
                    <flux:button variant="primary" icon="plus" wire:click="openCreateModal" class="w-full sm:w-auto shadow-sm">{{ __('Add New') }}</flux:button>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto px-4 pb-4 sm:px-6 pt-2">
            <flux:table>
                <flux:table.columns>
                    @if ($isCollege || $isSubject)
                        <flux:table.column>{{ $isSubject ? __('Subject Code') : __('Code') }}</flux:table.column>
                    @endif
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    @if ($isCourse)<flux:table.column>{{ __('Program Level') }}</flux:table.column>@endif
                    <flux:table.column>{{ $isCourse ? __('Affiliated Colleges') : __('Teachers Count') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Action') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($records as $record)
                        <flux:table.row wire:key="reference-{{ $type }}-{{ $record->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-200">

                            @if ($isCollege || $isSubject)
                                <flux:table.cell>
                                    <span class="font-mono text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $isSubject ? ($record->subject_code ?: '—') : ($record->code ?: '—') }}</span>
                                </flux:table.cell>
                            @endif

                            <flux:table.cell class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $record->name }}
                            </flux:table.cell>

                            @if ($isCourse)
                                <flux:table.cell>{{ $programLevels->firstWhere('slug', $record->level)?->name ?? $record->level }}</flux:table.cell>
                            @endif

                            <flux:table.cell>
                                <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-300">
                                    <flux:icon.users variant="micro" class="text-zinc-400" />
                                    <span>{{ $usageCounts->get($record->id, 0) }} {{ $isCourse ? __('colleges') : __('teachers') }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" :color="$record->is_active ? 'emerald' : 'zinc'">
                                    {{ $record->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $record->id }})" :title="__('Edit')" class="text-zinc-500 hover:text-indigo-600 dark:hover:text-indigo-400" />
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="confirmDelete({{ $record->id }})" :title="__('Delete')" class="text-zinc-500 hover:text-red-600 dark:hover:text-red-400" />
                                </div>
                            </flux:table.cell>

                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="{{ ($isCourse || $isCollege || $isSubject) ? 5 : 4 }}">
                                <div class="flex flex-col items-center justify-center py-12 text-zinc-500 dark:text-zinc-400">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 mb-3">
                                        <flux:icon.document-text class="h-6 w-6 text-zinc-400" />
                                    </div>
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ __('No records found') }}</p>
                                    <p class="text-sm mt-1">{{ __('Create a record or adjust your search.') }}</p>
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
                    <flux:heading size="lg" class="font-semibold">{{ $editingId ? __('Edit :title', ['title' => $title]) : __('Create :title', ['title' => $title]) }}</flux:heading>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 bg-white dark:bg-zinc-900">
                @if ($isCollege || $isSubject)
                    <div class="space-y-1">
                        <flux:input wire:model="code" :label="$isSubject ? __('Subject Code') : __('College Code')" :placeholder="$isSubject ? __('Enter subject code') : __('Enter value')" />
                        @error('code') <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="space-y-1">
                    <flux:input wire:model="name" :label="$title.__('Name')" :placeholder="__('Enter value')" required />
                    @error('name') <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                @if ($isCourse)
                    <div class="space-y-1">
                        <flux:select wire:model="level" :label="__('Program Level')" required>
                            <option value="">{{ __('Select') }}</option>
                            @foreach ($programLevels as $programLevel)
                                <option wire:key="course-level-{{ $programLevel->slug }}" value="{{ $programLevel->slug }}">{{ $programLevel->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('level') <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="pt-2">
                    <flux:switch wire:model="isActive" :label="__('Keep active')" :description="__('Inactive records are hidden from selection lists.')" />
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 border-t border-zinc-100 bg-zinc-50/50 px-6 py-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                <flux:button type="button" variant="ghost" wire:click="cancelEdit" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="check-circle" wire:loading.attr="disabled" class="w-full sm:w-auto shadow-sm">
                    {{ $editingId ? __('Update') : __('Save') }}
                </flux:button>
            </div>

        </form>
    </flux:modal>

    <flux:modal name="confirm-reference-data-deletion" wire:model="showDeleteModal" @close="cancelDelete" focusable class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $title }} Delete?</flux:heading>
                <flux:text class="mt-2">
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $deletingName }}</span>{{ __('Delete record') }}</flux:text>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button type="button" wire:click="cancelDelete">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed">
                    <span wire:loading.remove wire:target="deleteConfirmed">{{ __('Delete record') }}</span>
                    <span wire:loading wire:target="deleteConfirmed">{{ __('Delete record') }}</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

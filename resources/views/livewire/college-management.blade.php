<div class="p-4 sm:p-6">
    <!-- Main Card Container -->
    <flux:card class="p-0 sm:p-0 overflow-hidden shadow-sm">

        <!-- Header Section -->
        <div class="border-b border-zinc-200 bg-zinc-50/40 p-4 dark:border-zinc-700/50 dark:bg-zinc-900/40 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <!-- Total College count moved to header next to title -->
                    <div class="flex items-center gap-3">
                        <flux:heading size="xl" class="font-bold tracking-tight">{{ __('College Management') }}</flux:heading>
                        <flux:badge color="indigo" size="sm" class="font-medium shadow-sm">
                            {{ trans_choice(':count college|:count colleges', $colleges->total()) }}
                        </flux:badge>
                    </div>
                    <flux:subheading class="mt-1">{{ __('All Colleges') }}</flux:subheading>
                </div>

                <div class="flex-shrink-0">
                    @if(auth()->user()->isAdmin())
                        <flux:button variant="primary" icon="plus" :href="route('colleges.create')" wire:navigate class="shadow-sm">{{ __('Add College') }}</flux:button>
                    @elseif(auth()->user()->college_id)
                        <flux:button variant="primary" icon="pencil-square" :href="route('colleges.edit', auth()->user()->college_id)" wire:navigate class="shadow-sm">{{ __('Edit College Profile') }}</flux:button>
                    @endif
                </div>
            </div>

            <!-- Search & Filter Section -->
            <div class="mt-6 grid gap-3 lg:grid-cols-[minmax(16rem,1.5fr)_minmax(10rem,0.75fr)_minmax(10rem,0.75fr)_auto] lg:items-end">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :label="__('Search Colleges')"
                    :placeholder="__('Search by college name, code, or principal')"
                    class="w-full shadow-sm"
                />

                <flux:select wire:model.live="collegeTypeFilter" :label="__('College Type')">
                    <flux:select.option value="">{{ __('All Colleges') }}</flux:select.option>
                    <flux:select.option value="government">{{ __('Government') }}</flux:select.option>
                    <flux:select.option value="non_government">{{ __('Non-government') }}</flux:select.option>
                    <flux:select.option value="other">{{ __('Other') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="approvalStatusFilter" :label="__('Approval Status')">
                    <flux:select.option value="">{{ __('All Colleges') }}</flux:select.option>
                    <flux:select.option value="approved">{{ __('Approved') }}</flux:select.option>
                    <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
                    <flux:select.option value="rejected">{{ __('Rejected') }}</flux:select.option>
                </flux:select>

                <div class="flex flex-wrap items-center gap-2">
                    @if(auth()->user()->isAdmin())
                        <flux:button wire:click="toggleTrashed" :icon="$showTrashed ? 'building-office-2' : 'trash'" class="w-full sm:w-auto">
                            {{ $showTrashed ? __('Show Active Colleges') : __('View Trash') }}
                        </flux:button>
                    @endif
                </div>
            </div>

            @if(trim($search) !== '' || $collegeTypeFilter !== '' || $approvalStatusFilter !== '')
                <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-indigo-100 bg-indigo-50/70 px-3 py-2 dark:border-indigo-900 dark:bg-indigo-950/30">
                    <flux:text class="text-xs text-indigo-700 dark:text-indigo-300">{{ __('Showing colleges that match the active filters.') }}</flux:text>
                    <flux:button size="sm" variant="ghost" wire:click="clearFilters">{{ __('Clear Filters') }}</flux:button>
                </div>
            @endif
        </div>

        @if(auth()->user()->isAdmin() && count($selectedCollegeIds) > 0)
            <div class="flex flex-col gap-3 border-b border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <flux:text class="font-semibold text-indigo-900 dark:text-indigo-200">{{ trans_choice(':count college selected|:count colleges selected', count($selectedCollegeIds)) }}</flux:text>
                <div class="flex flex-wrap gap-2">
                    @if($showTrashed)
                        <flux:button size="sm" variant="primary" wire:click="restoreSelected">{{ __('Restore Selected') }}</flux:button>
                        <flux:button size="sm" variant="danger" wire:click="confirmBulkPermanentDeletion">{{ __('Delete selected') }}</flux:button>
                    @else
                        <flux:button size="sm" variant="danger" wire:click="confirmBulkDeletion">{{ __('Move Selected to Trash') }}</flux:button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Table Section (px-4 added here for breathing room) -->
        <div class="overflow-x-auto px-4 pb-4 sm:px-6 pt-2">
            <flux:table>
                <flux:table.columns>
                    @if(auth()->user()->isAdmin())
                        <flux:table.column class="w-12 text-center">
                            <input type="checkbox" wire:click="toggleSelectAllOnPage" @checked($selectAllOnPage) :aria-label="__('College')" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                        </flux:table.column>
                    @endif
                    <flux:table.column>{{ __('College') }}</flux:table.column>
                    <flux:table.column>{{ __('Location') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Computer Lab') }}</flux:table.column>
                    <flux:table.column>{{ __('Approve') }}</flux:table.column>
                    <flux:table.column>{{ __('Teacher') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Action') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($colleges as $college)
                        <flux:table.row wire:key="college-{{ $college->id }}" class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-200">

                            @if(auth()->user()->isAdmin())
                                <flux:table.cell class="text-center">
                                    <input type="checkbox" wire:model.live="selectedCollegeIds" value="{{ $college->id }}" aria-label="{{ $college->name }} select" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                </flux:table.cell>
                            @endif

                            <!-- College Name & Code with Icon -->
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-100 text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                                        <flux:icon.building-office-2 class="size-5" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $college->name }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ __('Code: ') }}<span class="font-mono font-medium">{{ $college->code ?: 'N/A' }}</span></span>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <!-- Location with Icon -->
                            <flux:table.cell class="whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-300">
                                    <flux:icon.map-pin variant="micro" class="text-zinc-400" />
                                    <span>{{ $college->thana?->name ?: '—' }}, {{ $college->district?->name ?: '—' }}</span>
                                </div>
                            </flux:table.cell>

                            <!-- College Type -->
                            <flux:table.cell class="whitespace-nowrap">
                                <flux:badge :color="$college->college_type === 'government' ? 'emerald' : 'zinc'" size="sm">
                                    {{ ['government'=>__('Government'),'non_government'=>__('Non-government'),'other'=>__('Other')][$college->college_type] ?? __('Not specified') }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Computer Lab -->
                            <flux:table.cell class="whitespace-nowrap">
                                @if($college->has_computer_lab)
                                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                        <flux:icon.check-circle variant="micro" />
                                        <span class="text-sm font-medium">{{ __('Yes') }}</span>
                                    </div>
                                @elseif($college->has_computer_lab === false)
                                    <div class="flex items-center gap-1.5 text-zinc-400 dark:text-zinc-500">
                                        <flux:icon.x-circle variant="micro" />
                                        <span class="text-sm">{{ __('No') }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-zinc-400">{{ __('Not specified') }}</span>
                                @endif
                            </flux:table.cell>

                            <!-- Approval Status -->
                            @php($approvalStatus = $college->approval_status)
                            <flux:table.cell class="whitespace-nowrap">
                                <flux:badge size="sm" :color="match($approvalStatus) {
                                    \App\Enums\ApprovalStatus::Approved => 'emerald',
                                    \App\Enums\ApprovalStatus::Rejected => 'rose',
                                    default => 'amber'
                                }">
                                    {{ match($approvalStatus) {
                                        \App\Enums\ApprovalStatus::Approved => __('Approved'),
                                        \App\Enums\ApprovalStatus::Rejected => __('Rejected'),
                                        default => __('Pending')
                                    } }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Teachers Count with Icon -->
                            <flux:table.cell class="whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300">
                                    <flux:icon.users variant="micro" class="text-zinc-400" />
                                    <span class="font-semibold">{{ $college->teachers_count }}</span> <span class="text-xs text-zinc-500">{{ __('Teachers') }}</span>
                                </div>
                            </flux:table.cell>

                            <!-- Minimized Actions Dropdown -->
                            <flux:table.cell class="whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($showTrashed)
                                        <flux:button variant="ghost" size="sm" icon="arrow-path" wire:click="restore({{ $college->id }})" title="{{ __('Restore') }}" class="text-emerald-600 hover:text-emerald-700" />
                                        <flux:button variant="ghost" size="sm" icon="trash" wire:click="confirmPermanentDeletion({{ $college->id }})" title="{{ __('Delete') }}" class="text-red-600 hover:text-red-700" />
                                    @else
                                        <flux:dropdown align="end">
                                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" class="text-zinc-500" />
                                            <flux:menu class="min-w-40">
                                                <flux:menu.item icon="eye" :href="route('colleges.show', $college)" wire:navigate>{{ __('View Profile') }}</flux:menu.item>
                                                <flux:menu.item icon="pencil-square" :href="route('colleges.edit', $college)" wire:navigate>{{ __('Edit Profile') }}</flux:menu.item>

                                                @if(auth()->user()->isAdmin())
                                                    @if($college->approval_status === \App\Enums\ApprovalStatus::Pending)
                                                        <flux:menu.separator />
                                                        <flux:menu.item icon="check-badge" wire:click="approveCollege({{ $college->id }})" class="text-emerald-600 hover:text-emerald-700">{{ __('Approve College') }}</flux:menu.item>
                                                    @endif
                                                    <flux:menu.separator />
                                                    <flux:menu.item icon="trash" wire:click="confirmDeletion({{ $college->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-500/10">{{ __('Move to Trash') }}</flux:menu.item>
                                                @endif
                                            </flux:menu>
                                        </flux:dropdown>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell :colspan="auth()->user()->isAdmin() ? 8 : 7">
                                <div class="flex flex-col items-center justify-center py-16 text-zinc-500 dark:text-zinc-400">
                                    <div class="rounded-full bg-zinc-100 p-4 dark:bg-zinc-800/50 mb-3">
                                        <flux:icon.building-library class="h-8 w-8 text-zinc-400" />
                                    </div>
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ __('No colleges found') }}</p>
                                    <p class="text-sm mt-1">{{ __('Create a college or adjust your search filters.') }}</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        @if($colleges->hasPages())
            <div class="border-t border-zinc-200 p-4 px-4 sm:px-6 dark:border-zinc-700/50 bg-zinc-50/30 dark:bg-zinc-900/30">
                {{ $colleges->links() }}
            </div>
        @endif

    </flux:card>

    <flux:modal name="confirm-college-deletion" focusable class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $permanentDeletion ? __('Permanently Delete College') : __('Move College to Trash') }}</flux:heading>
                <flux:text class="mt-2">
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $deletingCollegeName }}</span>
                    {{ $permanentDeletion ? __('This college will be permanently deleted.') : __('This college will be moved to trash.') }}
                </flux:text>
            </div>
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button wire:click="cancelDeletion">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed">
                    <span wire:loading.remove wire:target="deleteConfirmed">{{ $permanentDeletion ? __('Delete') : __('Delete') }}</span>
                    <span wire:loading wire:target="deleteConfirmed">{{ __('Deleting...') }}</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

<div class="w-full mx-auto py-6 sm:px-6 lg:px-8"
     x-data="{ showImportModal: false, showEditModal: false }"
     @close-modal.window="showImportModal = false"
     @open-edit-modal.window="showEditModal = true"
     @close-edit-modal.window="showEditModal = false"
     @teacher-selection-updated.window="$el.querySelectorAll('[data-teacher-checkbox]').forEach((checkbox) => checkbox.checked = $event.detail.selected)">

    <div class="bg-white dark:bg-slate-900 shadow-md rounded-lg overflow-hidden">

        <!-- Topbar: Search, Filter  Import button -->
        <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/80 p-4 sm:p-5">
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $isAdmin ? __('Teacher information') : __('Teacher information') }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $isAdmin ? __('Teacher information') : __('Teacher information') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <flux:button variant="primary" icon="plus" :href="route('teachers.create')" wire:navigate>{{ __('Teacher information') }}</flux:button>
                    <span class="inline-flex w-fit items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 shadow-sm">
                        Total {{ $teachers->total() }} teachers
                    </span>
                    @if($isAdmin)<span class="inline-flex w-fit items-center rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 shadow-sm">
                        Total {{ $collegeCount }}items College
                    </span>@endif
                </div>
            </div>

            <div @class(['grid gap-3 lg:items-end', 'lg:grid-cols-[minmax(16rem,1.25fr)_repeat(2,minmax(10rem,0.75fr))_auto]' => $isAdmin, 'lg:grid-cols-[minmax(16rem,1.5fr)_minmax(12rem,1fr)]' => ! $isAdmin])>

                <!-- Search input -->
                <div>
                    <label for="teacher-search" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">{{ __('Teacher information') }}</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"></path></svg>
                        <input
                            id="teacher-search"
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            :placeholder="__('College information')"
                            class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 py-2.5 pl-10 pr-3 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition placeholder:text-slate-400 dark:placeholder:text-slate-500 hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>
                </div>

                <!-- Filter options -->
                    <!-- Subject filter -->
                    <div>
                        <label for="subject-filter" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">{{ __('Information') }}</label>
                        <select id="subject-filter" wire:model.live="subjectFilter" class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">{{ __('Information') }}</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}">{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($isAdmin)
                    <!-- College code filter -->
                    <div>
                        <label for="college-filter" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">{{ __('College Code') }}</label>
                        <select id="college-filter" wire:model.live="collegeCodeFilter" class="block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-700 dark:text-slate-300 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="">{{ __('College information') }}</option>
                            @foreach($collegeCodes as $code)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                <!-- Import button -->
                @if($isAdmin)<div class="flex flex-col gap-2 sm:flex-row">
                    <button
                        type="button"
                        wire:click="toggleTrashed"
                        class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 lg:w-auto"
                    >
                        {{ $showTrashed ? __('Information') : __('Information') }}
                    </button>
                    <button
                        @click="showImportModal = true"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 lg:w-auto"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L7 9m5-5 5 5M5 20h14"></path></svg>{{ __('Information') }}</button>
                </div>@endif
            </div>

            @if(trim($search) !== '' || $subjectFilter !== '' || $collegeCodeFilter !== '')
                <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-indigo-100 bg-indigo-50/70 px-3 py-2 dark:border-indigo-900 dark:bg-indigo-950/30">
                    <p class="text-xs font-medium text-indigo-700 dark:text-indigo-300">{{ __('Information') }}</p>
                    <button type="button" wire:click="clearFilters" class="shrink-0 text-xs font-semibold text-indigo-700 underline decoration-indigo-300 underline-offset-4 transition hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-100">{{ __('Delete information') }}</button>
                </div>
            @endif
        </div>

        <!-- Data table -->
        @if ($isAdmin && count($selectedTeacherIds) > 0)
            <div class="flex flex-col gap-3 border-b border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">
                    {{ count($selectedTeacherIds) }} teachers selected
                </p>
                @if ($showTrashed)
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <flux:button variant="primary" size="sm" wire:click="restoreSelectedTeachers">{{ __('Select an option') }}</flux:button>
                        <flux:button variant="danger" size="sm" wire:click="confirmBulkPermanentDeletion">{{ __('Delete information') }}</flux:button>
                    </div>
                @else
                    <flux:button variant="danger" size="sm" wire:click="confirmBulkTeacherDeletion">{{ __('Delete information') }}</flux:button>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 dark:bg-slate-950 text-white">
                <tr>
                    @if($isAdmin)<th class="w-12 px-4 py-3 text-center">
                        <input
                            type="checkbox"
                            wire:click="toggleSelectAllOnPage"
                            data-teacher-checkbox
                            class="size-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500"
                            :aria-label="__('Teacher information')"
                            @checked($selectAllOnPage)
                        >
                    </th>@endif
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">TMIS ID</th>
                    @if($isAdmin)<th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('College Code') }}</th>@endif
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Teacher information') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Information') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Information') }}</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">{{ __('Approve') }}</th>
                    @can('teachers.assign-role')<th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">{{ __('Information') }}</th>@endcan
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">{{ __('Action') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 text-sm">
                @forelse ($teachers as $teacher)
                    <tr wire:key="teacher-row-{{ $teacher->id }}" class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                        @if($isAdmin)<td class="px-4 py-4 text-center">
                            <input
                                type="checkbox"
                                wire:key="teacher-select-{{ $teacher->id }}"
                                wire:click="toggleTeacherSelection({{ $teacher->id }})"
                                value="{{ $teacher->id }}"
                                data-teacher-checkbox
                                class="size-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500"
                                aria-label="{{ $teacher->display_name }} select"
                                @checked(in_array((string) $teacher->id, $selectedTeacherIds, true))
                            >
                        </td>@endif
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-slate-100">
                            {{ $teacher->tmis_id ?? 'N/A' }}
                        </td>
                        @if($isAdmin)<td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-slate-300">
                            {{ $teacher->college_code ?? '-' }}
                        </td>@endif
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-slate-100">
                            {{ $teacher->display_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-gray-800 dark:text-slate-200 font-semibold">{{ $teacher->designation }}</span>
                            <span class="block text-gray-500 dark:text-slate-400 text-xs">{{ $teacher->subject }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block text-gray-800 dark:text-slate-200">{{ $teacher->mobile_number ?? '-' }}</span>
                            <span class="block text-blue-600 text-xs">{{ $teacher->email ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($isAdmin && auth()->user()->can('teachers.approve') && ! $showTrashed)
                                <div class="inline-flex flex-col items-center gap-1.5">
                                    <flux:switch
                                        :checked="$teacher->approval_status === \App\Enums\ApprovalStatus::Approved"
                                        wire:click="toggleTeacherApproval({{ $teacher->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleTeacherApproval({{ $teacher->id }})"
                                        aria-label="{{ $teacher->display_name }}change approval"
                                    />
                                    <span @class([
                                        'text-xs font-semibold',
                                        'text-emerald-600 dark:text-emerald-400' => $teacher->approval_status === \App\Enums\ApprovalStatus::Approved,
                                        'text-red-600 dark:text-red-400' => $teacher->approval_status === \App\Enums\ApprovalStatus::Rejected,
                                        'text-amber-600 dark:text-amber-400' => $teacher->approval_status === \App\Enums\ApprovalStatus::Pending,
                                    ])>
                                        {{ match($teacher->approval_status) { \App\Enums\ApprovalStatus::Approved => __('Approval information'), \App\Enums\ApprovalStatus::Rejected => __('Information'), default => __('Information') } }}
                                    </span>
                                </div>
                            @else
                                <flux:badge :color="match($teacher->approval_status) { \App\Enums\ApprovalStatus::Approved => 'green', \App\Enums\ApprovalStatus::Rejected => 'red', default => 'amber' }">{{ match($teacher->approval_status) { \App\Enums\ApprovalStatus::Approved => __('Approval information'), \App\Enums\ApprovalStatus::Rejected => __('Information'), default => __('Information') } }}</flux:badge>
                            @endif
                        </td>
                        @can('teachers.assign-role')
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($teacher->user)
                                    <flux:select size="sm" wire:change="changeTeacherRole({{ $teacher->id }}, $event.target.value)" aria-label="{{ $teacher->display_name }}change role">
                                        <option value="teacher" @selected($teacher->user->role === \App\Enums\UserRole::Teacher)>{{ __('Teacher') }}</option>
                                        <option value="principal" @selected($teacher->user->role === \App\Enums\UserRole::Principal)>{{ __('College information') }}</option>
                                    </flux:select>
                                @else
                                    <flux:badge color="zinc">{{ __('Information') }}</flux:badge>
                                @endif
                            </td>
                        @endcan
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            @if ($showTrashed)
                                <button wire:click="restoreTeacher({{ $teacher->id }})" class="mr-2 rounded bg-emerald-100 px-3 py-1 text-emerald-700 transition hover:bg-emerald-200 hover:text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900">Restore</button>
                                <button wire:click="confirmPermanentTeacherDeletion({{ $teacher->id }})" class="rounded bg-red-100 px-3 py-1 text-red-700 transition hover:bg-red-200 hover:text-red-900 dark:bg-red-950/60 dark:text-red-300 dark:hover:bg-red-900">Permanent Delete</button>
                            @else
                                <flux:button size="sm" icon="eye" :href="route('teachers.show', $teacher)" wire:navigate>{{ __('View') }}</flux:button>
                                @can('teachers.update')<flux:button size="sm" icon="pencil-square" :href="route('teachers.edit', $teacher)" wire:navigate>{{ __('Edit') }}</flux:button>@endcan
                                @can('teachers.approve')
                                @if(! $isAdmin && $teacher->approval_status === \App\Enums\ApprovalStatus::Pending)
                                    <flux:button size="sm" variant="primary" wire:click="approveTeacher({{ $teacher->id }})">{{ __('Information') }}</flux:button>
                                    <flux:button size="sm" variant="danger" wire:click="rejectTeacher({{ $teacher->id }})">{{ __('Information') }}</flux:button>
                                @endif
                                @endcan
                                @can('teachers.delete')<button wire:click="confirmTeacherDeletion({{ $teacher->id }})" class="rounded bg-red-100 px-3 py-1 text-red-600 transition hover:bg-red-200 hover:text-red-900 dark:bg-red-950/60 dark:text-red-300 dark:hover:bg-red-900">Delete</button>@endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 9 : 6 }}" class="px-6 py-4 text-center text-gray-500 dark:text-slate-400">{{ __('Information') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-4 py-4 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-700 sm:px-6">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>

        <!-- Import modal (Alpine.js) -->
        <div
            x-show="showImportModal"
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto p-3 sm:p-6"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-full items-end justify-center sm:items-center">
                <!-- Background overlay -->
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/55 backdrop-blur-sm"
                    @click="showImportModal = false"
                    aria-hidden="true"
                ></div>

                <!-- Modal panel -->
                <div
                    x-show="showImportModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-xl transform overflow-hidden rounded-2xl border border-white/60 bg-white dark:bg-slate-900 text-left shadow-2xl transition-all"
                >
                    <!-- Close Button -->
                    <button type="button" @click="showImportModal = false" class="absolute right-4 top-4 z-10 inline-flex size-9 items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow-sm transition hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-300" :aria-label="__('Information')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="bg-white dark:bg-slate-900">
                        <!-- Previously created import component is called here -->
                        <livewire:teacher-data-import />
                    </div>
                </div>
            </div>
        </div>

        <flux:modal name="confirm-teacher-deletion" focusable class="max-w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ $permanentDeletion ? __('Teacher information') : __('Teacher information') }}
                    </flux:heading>
                    <flux:subheading class="mt-2">
                        @if ($permanentDeletion)
                            <strong>{{ $deletingTeacherName }}</strong>information will be permanently deleted. This action cannot be undone.
                        @else
                            <strong>{{ $deletingTeacherName }}</strong>information will be moved to trash and can be restored later.
                        @endif
                    </flux:subheading>
                </div>

                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <flux:modal.close>
                        <flux:button variant="filled" wire:click="cancelTeacherDeletion" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" wire:click="deleteTeacher" wire:loading.attr="disabled" wire:target="deleteTeacher" class="w-full sm:w-auto">
                        <span wire:loading.remove wire:target="deleteTeacher">{{ $permanentDeletion ? __('Delete information') : __('Information') }}</span>
                        <span wire:loading wire:target="deleteTeacher">{{ __('Delete information') }}</span>
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        <!-- Edit modal (Edit Modal) -->
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto p-3 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true" @keydown.escape.window="showEditModal = false">
            <div class="flex min-h-full items-end justify-center sm:items-center">
                <!-- Background Overlay -->
                <div
                    x-show="showEditModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/55 backdrop-blur-sm"
                    @click="showEditModal = false"
                    aria-hidden="true"
                ></div>

                <!-- Modal Panel -->
                <div
                    x-show="showEditModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
                    x-transition:leave-end="translate-y-6 opacity-0 sm:translate-y-0 sm:scale-95"
                    class="relative flex max-h-[calc(100vh-1.5rem)] w-full max-w-5xl transform flex-col overflow-hidden rounded-2xl border border-white/60 bg-slate-50 dark:bg-slate-950 text-left shadow-2xl transition-all sm:max-h-[calc(100vh-3rem)]"
                >

                    <div class="relative border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-4 sm:px-7 sm:py-5">
                        <!-- Close Button -->
                        <button type="button" @click="showEditModal = false" class="absolute right-4 top-4 inline-flex size-9 items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow-sm transition hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" :aria-label="__('Information')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="pr-12">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Teacher profile</p>
                            <h3 class="mt-1 text-xl font-bold text-slate-900 dark:text-slate-100 sm:text-2xl" id="modal-title">{{ __('Teacher information') }}</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Information') }}</p>
                        </div>
                    </div>

                        <!-- Edit form -->
                        <form wire:submit.prevent="updateTeacher" class="flex min-h-0 flex-1 flex-col">
                            <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-4 py-5 sm:px-7 sm:py-6">

                            @if ($errors->any())
                                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200" role="alert">
                                    <div class="flex items-start gap-3">
                                        <svg class="mt-0.5 size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"></path></svg>
                                        <div>
                                            <p class="text-sm font-semibold">{{ __('Information') }}</p>
                                            <p class="mt-0.5 text-xs text-red-700 dark:text-red-300">{{ __('Information') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('College information') }}</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('College Code') }}</label>
                                        <input type="text" wire:model="editForm.college_code" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.college_code') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="lg:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('College Name') }}</label>
                                        <input type="text" wire:model="editForm.college_name" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.college_name') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">TMIS ID</label>
                                        <input type="text" wire:model="editForm.tmis_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.tmis_id') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Teacher information') }}</legend>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Teacher information') }}</label>
                                        <input type="text" wire:model="editForm.name" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        @error('editForm.name') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Information') }}</label>
                                        <select wire:model="editForm.designation" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">{{ __('Select') }}</option>@foreach($designations as $designation)<option value="{{ $designation }}">{{ $designation }}</option>@endforeach</select>
                                        @error('editForm.designation') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Information') }}</label>
                                        <select wire:model="editForm.subject" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">{{ __('Select') }}</option>@foreach($subjects as $subject)<option value="{{ $subject }}">{{ $subject }}</option>@endforeach</select>
                                        @error('editForm.subject') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Teacher Level') }}</label>
                                        <select wire:model="editForm.teacher_level" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">{{ __('Select') }}</option>@foreach($teacherLevels as $level)<option value="{{ $level }}">{{ $level }}</option>@endforeach</select>
                                        @error('editForm.teacher_level') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Employment Type') }}</label>
                                        <select wire:model="editForm.employment_type" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm"><option value="">{{ __('Select') }}</option>@foreach($employments as $employment)<option value="{{ $employment }}">{{ $employment }}</option>@endforeach</select>
                                        @error('editForm.employment_type') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Training information') }}</legend>
                                    <flux:button type="button" size="sm" wire:click="addTrainingEntry">{{ __('Training information') }}</flux:button>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Training information') }}</p>
                                <div class="grid gap-3">
                                    @foreach ($trainingEntries as $index => $entry)
                                        <div wire:key="teacher-training-{{ $index }}" class="grid gap-3 rounded-lg border border-slate-200 p-3 dark:border-slate-700 sm:grid-cols-2 lg:grid-cols-4">
                                            <div><label class="block text-sm font-medium">{{ __('Training information') }}</label><select wire:model.live="trainingEntries.{{ $index }}.kind" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"><option value="catalog">{{ __('Training information') }}</option><option value="other">{{ __('Training information') }}</option></select></div>
                                            <div><label class="block text-sm font-medium">{{ __('Institute') }}</label><select wire:model.live="trainingEntries.{{ $index }}.training_institute_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"><option value="">{{ $entry['kind'] === 'other' ? __('Information') : __('Select') }}</option>@foreach ($trainingInstitutes as $institute)<option value="{{ $institute->id }}">{{ $institute->name }}</option>@endforeach</select></div>
                                            @if ($entry['kind'] === 'catalog')
                                                <div><label class="block text-sm font-medium">{{ __('Training Type') }}</label><select wire:model="trainingEntries.{{ $index }}.training_type_id" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"><option value="">{{ __('Select') }}</option>@foreach ($trainingTypes->where('training_institute_id', (int) $entry['training_institute_id']) as $trainingType)<option value="{{ $trainingType->id }}">{{ $trainingType->name }} — {{ $trainingType->duration_value }} {{ ['hours' => __('Hours'), 'days' => __('Days'), 'weeks' => __('Weeks'), 'months' => __('Months')][$trainingType->duration_unit] ?? '' }}</option>@endforeach</select>@error("trainingEntries.$index.training_type_id")<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</div>
                                            @else
                                                <div><label class="block text-sm font-medium">{{ __('Training information') }}</label><input type="text" wire:model="trainingEntries.{{ $index }}.name" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900">@error("trainingEntries.$index.name")<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</div>
                                                @if (blank($entry['training_institute_id']))<div><label class="block text-sm font-medium">{{ __('Information') }}</label><input type="text" wire:model="trainingEntries.{{ $index }}.institute_name" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"></div>@endif
                                                <div class="grid grid-cols-2 gap-2"><div><label class="block text-sm font-medium">{{ __('Duration') }}</label><input type="number" min="1" wire:model="trainingEntries.{{ $index }}.duration_value" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"></div><div><label class="block text-sm font-medium">{{ __('Information') }}</label><select wire:model="trainingEntries.{{ $index }}.duration_unit" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-2 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900"><option value="hours">{{ __('Hours') }}</option><option value="days">{{ __('Days') }}</option><option value="weeks">{{ __('Weeks') }}</option><option value="months">{{ __('Months') }}</option></select></div></div>
                                            @endif
                                            <div><label class="block text-sm font-medium">{{ __('Information') }}</label><input type="number" min="1950" max="{{ date('Y') + 1 }}" wire:model="trainingEntries.{{ $index }}.training_year" placeholder="{{ date('Y') }}" class="mt-1.5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900">@error("trainingEntries.$index.training_year")<span class="mt-1 block text-xs text-red-600">{{ $message }}</span>@enderror</div>
                                            <div class="flex items-end"><flux:button type="button" size="sm" variant="danger" wire:click="removeTrainingEntry({{ $index }})">{{ __('Information') }}</flux:button></div>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            <fieldset class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 shadow-sm sm:p-5">
                                <legend class="px-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Information') }}</legend>
                                <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Information') }}</label>
                                    <input type="text" wire:model="editForm.mobile_number" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('editForm.mobile_number') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Email') }}</label>
                                    <input type="email" wire:model="editForm.email" class="mt-1.5 block w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm transition hover:border-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    @error('editForm.email') <span class="mt-1.5 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </div>
                                </div>
                            </fieldset>

                            </div>

                            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-4 sm:flex-row sm:justify-end sm:px-7">
                                <button type="button" @click="showEditModal = false" class="inline-flex w-full justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 sm:w-auto">{{ __('Cancel') }}</button>
                                <button type="submit" class="inline-flex w-full justify-center rounded-lg border border-transparent bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto" wire:loading.attr="disabled" wire:target="updateTeacher">
                                    <span wire:loading wire:target="updateTeacher" class="mr-2">{{ __('Information') }}</span>{{ __('Update') }}</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
</div>

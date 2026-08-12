<div class="mx-auto w-full px-4 py-6 sm:px-6 lg:px-8"
     x-data="{ showImportModal: false }"
     @close-modal.window="showImportModal = false"
     @teacher-selection-updated.window="$el.querySelectorAll('[data-teacher-checkbox]').forEach((checkbox) => checkbox.checked = $event.detail.selected)">

    <!-- ========================================== -->
    <!-- Main Card Container -->
    <!-- ========================================== -->
    <flux:card class="p-0 overflow-hidden shadow-sm">

        <!-- Topbar: Header, Search, Filter & Actions -->
        <div class="border-b border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700/50 dark:bg-zinc-900/40 sm:p-5">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <flux:heading size="xl" class="font-bold tracking-tight">{{ $isAdmin ? __('Teacher Management') : __('My College Teachers') }}</flux:heading>
                    <flux:text class="mt-1 text-sm">{{ $isAdmin ? __('Search, filter, approve, import, and manage teacher profiles.') : __('Review and manage teacher profiles for your college.') }}</flux:text>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge color="zinc" class="shadow-sm">Total {{ $teachers->total() }} teachers</flux:badge>
                    @if($isAdmin)
                        <flux:badge color="indigo" class="shadow-sm">{{ trans_choice(':count college|:count colleges', $collegeCount) }}</flux:badge>
                    @endif
                    @can('teachers.create')
                        <flux:button variant="primary" icon="plus" :href="route('teachers.create')" wire:navigate size="sm" class="shadow-sm">
                            {{ __('Add Teacher') }}
                        </flux:button>
                    @endcan
                </div>
            </div>

            <!-- Filters & Search Grid -->
            <div @class(['grid gap-4 lg:items-end', 'lg:grid-cols-[1.5fr_1fr_1fr_auto]' => $isAdmin, 'lg:grid-cols-[2fr_1fr]' => ! $isAdmin])>

                <!-- Search Input -->
                <div class="w-full">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        label="{{ __('Teacher') }}"
                        placeholder="{{ __('Search by name, email, or college') }}"
                        icon="magnifying-glass"
                    />
                </div>

                <!-- Subject Filter -->
                <div class="w-full">
                    <flux:field>
                        <flux:label>{{ __('Subject') }}</flux:label>
                        <div wire:ignore>
                            <select
                                data-searchable-select
                                data-teacher-filter
                                data-livewire-model="subjectFilter"
                                data-placeholder="{{ __('All Subjects') }}"
                                data-search-placeholder="{{ __('Search subjects') }}"
                                data-no-results-text="{{ __('No subjects found') }}"
                            >
                                <option value="">{{ __('All Subjects') }}</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject }}">{{ $subject }}</option>
                                @endforeach
                            </select>
                        </div>
                    </flux:field>
                </div>

                @if($isAdmin)
                    <!-- College Code Filter -->
                    <div class="w-full">
                        <flux:field>
                            <flux:label>{{ __('College Code') }}</flux:label>
                            <div wire:ignore>
                                <select
                                    data-searchable-select
                                    data-teacher-filter
                                    data-livewire-model="collegeCodeFilter"
                                    data-placeholder="{{ __('All College Codes') }}"
                                    data-search-placeholder="{{ __('Search college codes') }}"
                                    data-no-results-text="{{ __('No college codes found') }}"
                                >
                                    <option value="">{{ __('All College Codes') }}</option>
                                    @foreach($collegeCodes as $code)
                                        <option value="{{ $code }}">{{ $code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </flux:field>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 w-full pt-6 lg:pt-0">
                        <flux:button wire:click="toggleTrashed" variant="outline" class="w-full sm:w-auto">
                            {{ $showTrashed ? __('Show Active') : __('View Trash') }}
                        </flux:button>
                        <flux:button @click="showImportModal = true" variant="primary" icon="arrow-down-tray" class="w-full sm:w-auto">
                            {{ __('Import') }}
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Filter Status Indicator -->
            @if(trim($search) !== '' || $subjectFilter !== '' || $collegeCodeFilter !== '')
                <div class="mt-4 flex items-center justify-between rounded-lg border border-indigo-100 bg-indigo-50/70 px-4 py-2.5 dark:border-indigo-900/50 dark:bg-indigo-900/20">
                    <flux:text class="text-xs font-medium text-indigo-700 dark:text-indigo-300">
                        {{ __('Showing results that match the active search or filters.') }}
                    </flux:text>
                    <button type="button" wire:click="clearFilters" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline underline-offset-2 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                        {{ __('Clear Filters') }}
                    </button>
                </div>
            @endif
        </div>

        <!-- Bulk Actions Bar -->
        @if ($isAdmin && count($selectedTeacherIds) > 0)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-indigo-50/80 dark:bg-indigo-900/20 border-b border-indigo-100 dark:border-indigo-800/50 px-5 py-3">
                <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-300">
                    {{ count($selectedTeacherIds) }} teachers selected
                </p>
                <div class="flex flex-wrap gap-2">
                    @if ($showTrashed)
                        <flux:button variant="primary" size="sm" wire:click="restoreSelectedTeachers">{{ __('Restore Selected') }}</flux:button>
                        <flux:button variant="danger" size="sm" wire:click="confirmBulkPermanentDeletion">{{ __('Delete Permanently') }}</flux:button>
                    @else
                        <flux:button variant="danger" size="sm" wire:click="confirmBulkTeacherDeletion">{{ __('Move to Trash') }}</flux:button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <flux:table class="px-4">
                <flux:table.columns>
                    @if($isAdmin)
                        <flux:table.column class="w-12 px-4 py-3">
                            <flux:checkbox wire:click="toggleSelectAllOnPage" data-teacher-checkbox :checked="$selectAllOnPage" />
                        </flux:table.column>
                    @endif
                    <flux:table.column>{{ __('Teacher Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Designation & Subject') }}</flux:table.column>
                    <flux:table.column>{{ __('Contact') }}</flux:table.column>
                    <flux:table.column class="text-center">{{ __('Approve') }}</flux:table.column>
                    @can('teachers.assign-role')
                        <flux:table.column class="text-center">{{ __('Role') }}</flux:table.column>
                    @endcan
                    <flux:table.column class="text-right">{{ __('Action') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($teachers as $teacher)
                        <flux:table.row wire:key="teacher-row-{{ $teacher->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                            @if($isAdmin)
                                <flux:table.cell class="px-4 py-3">
                                    <flux:checkbox
                                        wire:key="teacher-select-{{ $teacher->id }}"
                                        wire:click="toggleTeacherSelection({{ $teacher->id }})"
                                        data-teacher-checkbox
                                        :checked="in_array((string) $teacher->id, $selectedTeacherIds, true)"
                                    />
                                </flux:table.cell>
                            @endif

                                <flux:table.cell>
                                    <div class="flex items-center gap-3">

                                        <!-- Profile Image Avatar -->
                                        <div class="shrink-0 flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800">
                                            @if($teacher?->user?->picture)
                                                <img src="{{ asset('storage/' . $teacher->user->picture) }}" alt="{{ $teacher->display_name }}" class="h-full w-full object-cover" />
                                            @else
                                                <span class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 uppercase">
                                                    {{ mb_substr($teacher?->display_name ?: 'U', 0, 1) }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Teacher Name & College -->
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $teacher?->display_name ?: __('No teacher profile') }}
                                            </span>
                                            <div class="flex items-center gap-1.5 mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                                <flux:icon.building-library variant="micro" class="size-3.5" />
                                                <span class="truncate max-w-[200px]">{{ $teacher?->college?->name ?: $user->college?->name ?: __('No college assigned') }}</span>
                                            </div>
                                        </div>

                                    </div>
                                </flux:table.cell>

                            <flux:table.cell>
                                <span class="block font-medium text-zinc-800 dark:text-zinc-200">{{ $teacher->designation?->name }}</span>
                                <span class="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $teacher->subject?->name }}</span>
                            </flux:table.cell>

                            <flux:table.cell>
                                <span class="block font-medium text-zinc-800 dark:text-zinc-200">{{ $teacher->user?->mobile_no ?? '-' }}</span>
                                <span class="block text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $teacher->user?->email ?? '-' }}</span>
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                @if($isAdmin && auth()->user()->can('teachers.approve') && ! $showTrashed)
                                    <div class="flex flex-col items-center gap-1">
                                        <flux:switch
                                            :checked="$teacher->approval_status === \App\Enums\ApprovalStatus::Approved"
                                            wire:click="toggleTeacherApproval({{ $teacher->id }})"
                                            wire:loading.attr="disabled"
                                        />
                                        <span @class([
                                            'text-[10px] font-bold uppercase tracking-wider',
                                            'text-emerald-600 dark:text-emerald-400' => $teacher->approval_status === \App\Enums\ApprovalStatus::Approved,
                                            'text-red-600 dark:text-red-400' => $teacher->approval_status === \App\Enums\ApprovalStatus::Rejected,
                                            'text-amber-600 dark:text-amber-400' => $teacher->approval_status === \App\Enums\ApprovalStatus::Pending,
                                        ])>
                                            {{ match($teacher->approval_status) { \App\Enums\ApprovalStatus::Approved => __('Approved'), \App\Enums\ApprovalStatus::Rejected => __('Rejected'), default => __('Pending') } }}
                                        </span>
                                    </div>
                                @else
                                    <flux:badge size="sm" :color="match($teacher->approval_status) { \App\Enums\ApprovalStatus::Approved => 'green', \App\Enums\ApprovalStatus::Rejected => 'red', default => 'amber' }">
                                        {{ match($teacher->approval_status) { \App\Enums\ApprovalStatus::Approved => __('Approved'), \App\Enums\ApprovalStatus::Rejected => __('Rejected'), default => __('Pending') } }}
                                    </flux:badge>
                                @endif
                            </flux:table.cell>

                            @can('teachers.assign-role')
                                <flux:table.cell class="text-center">
                                    @if($teacher->user)
                                        <flux:select size="sm" wire:change="changeTeacherRole({{ $teacher->id }}, $event.target.value)" class="min-w-[120px]">
                                            <option value="teacher" @selected($teacher->user->hasRole('teacher'))>{{ __('Teacher') }}</option>
                                            <option value="principal" @selected($teacher->user->hasRole('principal'))>{{ __('Principal') }}</option>
                                        </flux:select>
                                    @else
                                        <flux:badge color="zinc" size="sm">{{ __('No Account') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                            @endcan

                            <flux:table.cell class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($showTrashed)
                                        <flux:button size="sm" variant="outline" wire:click="restoreTeacher({{ $teacher->id }})">Restore</flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="confirmPermanentTeacherDeletion({{ $teacher->id }})">Delete</flux:button>
                                    @else
                                        <flux:dropdown align="end">
                                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="text-zinc-500" />
                                            <flux:menu>
                                                <flux:menu.item icon="eye" :href="route('teachers.show', $teacher)" wire:navigate>{{ __('View Profile') }}</flux:menu.item>
                                                @can('teachers.update')
                                                    <flux:menu.item icon="pencil-square" :href="route('teachers.edit', $teacher)" wire:navigate>{{ __('Edit Profile') }}</flux:menu.item>
                                                @endcan

                                                @can('teachers.approve')
                                                    @if($teacher->approval_status === \App\Enums\ApprovalStatus::Pending)
                                                        <flux:menu.separator />
                                                        <flux:menu.item icon="check-circle" wire:click="approveTeacher({{ $teacher->id }})" class="text-emerald-600 hover:text-emerald-700">{{ __('Approve') }}</flux:menu.item>
                                                        <flux:menu.item icon="x-circle" wire:click="rejectTeacher({{ $teacher->id }})" class="text-red-600 hover:text-red-700">{{ __('Reject') }}</flux:menu.item>
                                                    @endif
                                                @endcan

                                                @can('teachers.delete')
                                                    <flux:menu.separator />
                                                    <flux:menu.item icon="trash" wire:click="confirmTeacherDeletion({{ $teacher->id }})" class="text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10">{{ __('Move to Trash') }}</flux:menu.item>
                                                @endcan
                                            </flux:menu>
                                        </flux:dropdown>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="{{ $isAdmin ? 9 : 6 }}">
                                <div class="flex flex-col items-center justify-center py-12 text-zinc-500">
                                    <flux:icon.users class="size-10 text-zinc-300 dark:text-zinc-600 mb-3" />
                                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ __('No teachers found.') }}</p>
                                    <p class="text-sm mt-1">Try adjusting your search or filters.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        @if($teachers->hasPages())
            <div class="border-t border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700/50 dark:bg-zinc-800/20 sm:px-6">
                {{ $teachers->links() }}
            </div>
        @endif
    </flux:card>


    <!-- ========================================== -->
    <!-- Import Modal (Alpine + Livewire) -->
    <!-- ========================================== -->
    <div x-cloak x-show="showImportModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 bg-zinc-950/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showImportModal"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="showImportModal = false"
                     class="relative transform overflow-hidden rounded-xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-zinc-200 dark:border-zinc-700">

                    <div class="absolute right-4 top-4">
                        <flux:button variant="ghost" size="sm" icon="x-mark" @click="showImportModal = false" class="text-zinc-400 hover:text-zinc-600" />
                    </div>
                    <div class="p-1">
                        <livewire:teacher-data-import />
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- Delete Confirmation Modal (Flux) -->
    <!-- ========================================== -->
    <flux:modal name="confirm-teacher-deletion" class="max-w-md p-6">
        <div class="text-center sm:text-left">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20 sm:mx-0 sm:h-10 sm:w-10">
                <flux:icon.exclamation-triangle class="size-6 text-red-600 dark:text-red-400" />
            </div>
            <div class="mt-3 sm:mt-4">
                <flux:heading size="lg" class="font-bold text-zinc-900 dark:text-zinc-100">
                    {{ $permanentDeletion ? __('Permanently Delete Teacher') : __('Move Teacher to Trash') }}
                </flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    @if ($permanentDeletion)
                        <strong>{{ $deletingTeacherName }}</strong>'s information will be permanently deleted. This action cannot be undone.
                    @else
                        <strong>{{ $deletingTeacherName }}</strong>'s information will be moved to trash and can be restored later.
                    @endif
                </flux:text>
            </div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <flux:modal.close>
                <flux:button variant="outline" wire:click="cancelTeacherDeletion" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="deleteTeacher" wire:loading.attr="disabled" class="w-full sm:w-auto">
                <span wire:loading.remove wire:target="deleteTeacher">{{ $permanentDeletion ? __('Delete Permanently') : __('Move to Trash') }}</span>
                <span wire:loading wire:target="deleteTeacher">{{ __('Deleting...') }}</span>
            </flux:button>
        </div>
    </flux:modal>


    <!-- ========================================== -->
    <!-- Edit Teacher Modal (Flux UI + Alpine) -->
    <!-- ========================================== -->
    <flux:modal wire:model="showEditModal" name="edit-teacher-modal" class="max-w-4xl p-0 overflow-hidden" @close="showEditModal = false">

        <!-- Modal Header -->
        <div class="border-b border-zinc-200 bg-zinc-50/50 px-6 py-4 dark:border-zinc-700/50 dark:bg-zinc-900/40">
            <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Teacher Profile</p>
            <flux:heading size="xl" class="mt-1 font-bold">{{ __('Edit Teacher Profile') }}</flux:heading>
            <flux:text class="mt-1 text-sm">{{ __('Update teacher, college, training, and contact details.') }}</flux:text>
        </div>

        <!-- Form Body -->
        <form wire:submit.prevent="updateTeacher" class="flex flex-col max-h-[70vh]">
            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-white dark:bg-zinc-900">

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-900/20">
                        <div class="flex items-start gap-3">
                            <flux:icon.exclamation-circle class="size-5 text-red-600 dark:text-red-400 mt-0.5" />
                            <div>
                                <h3 class="text-sm font-semibold text-red-800 dark:text-red-300">{{ __('Unable to update teacher') }}</h3>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-400">{{ __('Please fix the highlighted fields below and try again.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- College Section -->
                <flux:fieldset>
                    <flux:legend>{{ __('College Details') }}</flux:legend>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mt-4">
                        <flux:input wire:model="editForm.college_code" label="{{ __('College Code') }}" />
                        <div class="lg:col-span-2">
                            <flux:input wire:model="editForm.college_name" label="{{ __('College Name') }}" />
                        </div>
                    </div>
                </flux:fieldset>

                <flux:separator variant="subtle" />

                <!-- Teacher Section -->
                <flux:fieldset>
                    <flux:legend>{{ __('Professional Details') }}</flux:legend>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-4">
                        <div class="lg:col-span-2">
                            <flux:input wire:model="editForm.name" label="{{ __('Teacher Name') }}" />
                        </div>
                        <flux:select wire:model="editForm.designation" label="{{ __('Designation') }}" placeholder="{{ __('Select') }}">
                            @foreach($designations as $designation)
                                <option value="{{ $designation }}">{{ $designation }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="editForm.subject" label="{{ __('Subject') }}" placeholder="{{ __('Select') }}">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}">{{ $subject }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="editForm.teacher_level" label="{{ __('Teacher Level') }}" placeholder="{{ __('Select') }}">
                            @foreach($teacherLevels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="editForm.employment_type" label="{{ __('Employment Type') }}" placeholder="{{ __('Select') }}">
                            @foreach($employments as $employment)
                                <option value="{{ $employment }}">{{ $employment }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </flux:fieldset>

                <flux:separator variant="subtle" />

                <!-- Training History Section -->
                <flux:fieldset>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <flux:legend>{{ __('Training History') }}</flux:legend>
                            <flux:text class="text-xs mt-1">{{ __('Keep ICT and professional development records current.') }}</flux:text>
                        </div>
                        <flux:button size="sm" icon="plus" wire:click="addTrainingEntry">{{ __('Add Training') }}</flux:button>
                    </div>

                    <div class="space-y-4">
                        @foreach ($trainingEntries as $index => $entry)
                            <div wire:key="teacher-training-{{ $index }}" class="relative rounded-lg border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700 dark:bg-zinc-800/30">

                                <button type="button" wire:click="removeTrainingEntry({{ $index }})" class="absolute -right-2 -top-2 flex size-6 items-center justify-center rounded-full bg-white text-red-500 shadow-sm ring-1 ring-zinc-200 hover:bg-red-50 dark:bg-zinc-800 dark:ring-zinc-700 transition">
                                    <flux:icon.x-mark variant="micro" />
                                </button>

                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <flux:select wire:model.live="trainingEntries.{{ $index }}.kind" label="{{ __('Training Source') }}">
                                        <option value="catalog">{{ __('Catalog Training') }}</option>
                                        <option value="other">{{ __('Custom Training') }}</option>
                                    </flux:select>

                                    <flux:select wire:model.live="trainingEntries.{{ $index }}.training_institute_id" label="{{ __('Institute') }}">
                                        <option value="">{{ $entry['kind'] === 'other' ? __('No institute selected') : __('Select') }}</option>
                                        @foreach ($trainingInstitutes as $institute)
                                            <option value="{{ $institute->id }}">{{ $institute->name }}</option>
                                        @endforeach
                                    </flux:select>

                                    @if ($entry['kind'] === 'catalog')
                                        <flux:select wire:model="trainingEntries.{{ $index }}.training_type_id" label="{{ __('Training Type') }}">
                                            <option value="">{{ __('Select') }}</option>
                                            @foreach ($trainingTypes->where('training_institute_id', (int) $entry['training_institute_id']) as $trainingType)
                                                <option value="{{ $trainingType->id }}">{{ $trainingType->name }} — {{ $trainingType->duration_value }} {{ ['hours' => __('Hours'), 'days' => __('Days'), 'weeks' => __('Weeks'), 'months' => __('Months')][$trainingType->duration_unit] ?? '' }}</option>
                                            @endforeach
                                        </flux:select>
                                    @else
                                        <flux:input wire:model="trainingEntries.{{ $index }}.name" label="{{ __('Training Name') }}" />

                                        @if (blank($entry['training_institute_id']))
                                            <flux:input wire:model="trainingEntries.{{ $index }}.institute_name" label="{{ __('Institute Name') }}" />
                                        @endif

                                        <div class="grid grid-cols-2 gap-3">
                                            <flux:input type="number" min="1" wire:model="trainingEntries.{{ $index }}.duration_value" label="{{ __('Duration') }}" />
                                            <flux:select wire:model="trainingEntries.{{ $index }}.duration_unit" label="{{ __('Unit') }}">
                                                <option value="hours">{{ __('Hours') }}</option>
                                                <option value="days">{{ __('Days') }}</option>
                                                <option value="weeks">{{ __('Weeks') }}</option>
                                                <option value="months">{{ __('Months') }}</option>
                                            </flux:select>
                                        </div>
                                    @endif

                                    <flux:input type="number" min="1950" max="{{ date('Y') + 1 }}" wire:model="trainingEntries.{{ $index }}.training_year" placeholder="{{ date('Y') }}" label="{{ __('Training Year') }}" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:fieldset>

                <flux:separator variant="subtle" />

                <!-- Contact Section -->
                <flux:fieldset>
                    <flux:legend>{{ __('Contact Details') }}</flux:legend>
                    <div class="grid gap-5 sm:grid-cols-2 mt-4">
                        <flux:input wire:model="editForm.mobile_number" label="{{ __('Mobile Number') }}" />
                        <flux:input type="email" wire:model="editForm.email" label="{{ __('Email') }}" />
                    </div>
                </flux:fieldset>

            </div>

            <!-- Form Footer -->
            <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 bg-zinc-50/80 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900/50 sm:flex-row sm:justify-end">
                <flux:button type="button" variant="ghost" @click="showEditModal = false" class="w-full sm:w-auto">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" class="w-full sm:w-auto">
                    <span wire:loading wire:target="updateTeacher" class="mr-2">{{ __('Updating...') }}</span>
                    <span wire:loading.remove wire:target="updateTeacher">{{ __('Update Profile') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

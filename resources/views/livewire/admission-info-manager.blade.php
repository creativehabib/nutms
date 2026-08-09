<div class="mx-auto w-full max-w-7xl p-4 sm:p-6">
    <flux:card class="p-0 overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">

        <!-- ========================================== -->
        <!-- Header Section -->
        <!-- ========================================== -->
        <div class="border-b border-zinc-200 bg-zinc-50/50 px-6 py-6 dark:border-zinc-700 dark:bg-zinc-800/30">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white border border-zinc-200 text-indigo-600 shadow-sm dark:bg-zinc-800 dark:border-zinc-700 dark:text-indigo-400">
                        <flux:icon.table-cells class="size-6" />
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <flux:heading size="xl" class="font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                                {{ __('Manage Admission Data') }}
                            </flux:heading>
                            <flux:badge color="indigo" size="sm" class="font-medium shadow-sm">
                                <!-- এখানে $records এর বদলে $colleges দেওয়া হয়েছে -->
                                {{ $colleges->total() }} {{ __('Colleges') }}
                            </flux:badge>
                        </div>
                        <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('View, edit, or delete Banbais honors admission information.') }}
                        </flux:text>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex w-full sm:w-auto items-center gap-3">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search records...') }}" class="w-full sm:w-64" />
                    <flux:modal.trigger name="import-admission-modal">
                        <flux:button variant="primary" icon="cloud-arrow-up" class="shrink-0 shadow-sm">
                            {{ __('Import') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- Expandable Data Table (Professional SaaS Style) -->
        <!-- ========================================== -->
        <div class="overflow-x-auto bg-white dark:bg-zinc-900 px-4 sm:px-6 py-2" x-data="{ expandedCollege: null }">
            <table class="w-full text-left whitespace-nowrap text-sm">
                <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400">
                    <th class="px-4 py-4 font-semibold">{{ __('College Info') }}</th>
                    <th class="px-4 py-4 font-semibold text-center">{{ __('Total Subjects') }}</th>
                    <th class="px-4 py-4 font-semibold text-center">{{ __('Total 21-22') }}</th>
                    <th class="px-4 py-4 font-semibold text-center">{{ __('Total 22-23') }}</th>
                    <th class="px-4 py-4 font-semibold text-center">{{ __('Total 23-24') }}</th>
                    <th class="px-4 py-4 font-semibold text-center">{{ __('Total 24-25') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($colleges as $college)
                    <!-- Main College Row -->
                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 cursor-pointer transition-colors"
                        @click="expandedCollege = expandedCollege === '{{ $college->college_code }}' ? null : '{{ $college->college_code }}'">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
                                    <flux:icon.chevron-down class="size-4 text-zinc-500 transition-transform duration-200" x-bind:class="expandedCollege === '{{ $college->college_code }}' ? 'rotate-180' : ''" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $college->college_name }}</span>
                                    <span class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mt-0.5">CODE: {{ $college->college_code }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ $college->total_subjects }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">{{ number_format($college->sum_21_22) }}</td>
                        <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">{{ number_format($college->sum_22_23) }}</td>
                        <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">{{ number_format($college->sum_23_24) }}</td>
                        <td class="px-4 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($college->sum_24_25) }}</td>
                    </tr>

                    <!-- Sub-table (Expanded View) -->
                    <tr x-show="expandedCollege === '{{ $college->college_code }}'" x-transition class="bg-zinc-50/50 dark:bg-zinc-900/30 border-b border-zinc-200 dark:border-zinc-700">
                        <td colspan="6" class="p-0">
                            <div class="px-10 py-5">
                                <table class="w-full text-left text-sm bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold text-zinc-600 dark:text-zinc-300">{{ __('Subject') }}</th>
                                        <th class="px-4 py-3 font-semibold text-center text-zinc-600 dark:text-zinc-300">{{ __('Sess 21-22') }}</th>
                                        <th class="px-4 py-3 font-semibold text-center text-zinc-600 dark:text-zinc-300">{{ __('Sess 22-23') }}</th>
                                        <th class="px-4 py-3 font-semibold text-center text-zinc-600 dark:text-zinc-300">{{ __('Sess 23-24') }}</th>
                                        <th class="px-4 py-3 font-semibold text-center text-zinc-600 dark:text-zinc-300">{{ __('Sess 24-25') }}</th>
                                        <th class="px-5 py-3 font-semibold text-right text-zinc-600 dark:text-zinc-300">{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($subjects[$college->college_code] ?? [] as $record)
                                        <tr class="border-b border-zinc-100 dark:border-zinc-700/50 last:border-0 hover:bg-zinc-50 dark:hover:bg-zinc-700/30">
                                            <td class="px-5 py-3">
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $record->subject_name }}</span>
                                                    <span class="text-[11px] font-mono text-zinc-500 mt-0.5">ID: {{ $record->subject_id }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">{{ $record->sess_21_22_total_admited }}</td>
                                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">{{ $record->sess_22_23_total_admited }}</td>
                                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">{{ $record->sess_23_24_total_admited }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400">{{ $record->sess_24_25_total_admited }}</td>
                                            <td class="px-5 py-3 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" wire:click.stop="edit({{ $record->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-indigo-600 dark:hover:bg-zinc-700 transition">
                                                        <flux:icon.pencil-square class="size-4.5" />
                                                    </button>
                                                    <button type="button" wire:click.stop="confirmDelete({{ $record->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg text-zinc-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 transition">
                                                        <flux:icon.trash class="size-4.5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="flex flex-col items-center justify-center py-16 text-center bg-zinc-50/50 dark:bg-zinc-900/30">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                                    <flux:icon.magnifying-glass class="size-8 text-zinc-400 dark:text-zinc-500" />
                                </div>
                                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('No records found') }}</h3>
                                <p class="mt-1 text-sm text-zinc-500">{{ __('Try adjusting your search query.') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- এখানে $records এর বদলে $colleges দেওয়া হয়েছে -->
        @if($colleges->hasPages())
            <div class="border-t border-zinc-200 px-6 py-4 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/20">
                {{ $colleges->links() }}
            </div>
        @endif
    </flux:card>

    <!-- ========================================== -->
    <!-- Edit Modal (Clean & Professional UI) -->
    <!-- ========================================== -->
    <flux:modal name="edit-admission-modal" class="max-w-3xl p-6 sm:p-8" @close="$wire.cancelEdit()">

        <!-- Header -->
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#eff2ff] text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                <flux:icon.pencil-square class="size-6" />
            </div>
            <div>
                <flux:heading size="lg" class="font-bold">{{ __('Edit Admission Record') }}</flux:heading>
                <flux:text class="mt-1 text-sm">{{ __('Update college or subject details below.') }}</flux:text>
            </div>
        </div>

        <form wire:submit.prevent="update" class="space-y-6">

            <!-- College & Subject Info -->
            <div>
                <flux:heading size="sm" class="mb-3 font-semibold text-zinc-800 dark:text-zinc-200">{{ __('General Information') }}</flux:heading>
                <div class="grid sm:grid-cols-2 gap-5">
                    <flux:input wire:model="college_code" label="{{ __('College Code') }}" required />
                    <flux:input wire:model="college_name" label="{{ __('College Name') }}" required />
                    <flux:input wire:model="subject_id" label="{{ __('Subject ID') }}" required />
                    <flux:input wire:model="subject_name" label="{{ __('Subject Name') }}" required />
                </div>
            </div>

            <!-- Session Data -->
            <div class="pt-2">
                <flux:heading size="sm" class="mb-3 font-semibold text-zinc-800 dark:text-zinc-200">{{ __('Admitted Students by Session') }}</flux:heading>
                <div class="grid sm:grid-cols-4 gap-4 p-5 rounded-xl border border-zinc-200 bg-zinc-50/50 dark:border-zinc-700 dark:bg-zinc-800/30">
                    <flux:input type="number" wire:model="sess_21_22" label="{{ __('2021-22') }}" required />
                    <flux:input type="number" wire:model="sess_22_23" label="{{ __('2022-23') }}" required />
                    <flux:input type="number" wire:model="sess_23_24" label="{{ __('2023-24') }}" required />
                    <flux:input type="number" wire:model="sess_24_25" label="{{ __('2024-25') }}" required />
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex gap-3 justify-end pt-4">
                <flux:button type="button" variant="ghost" wire:click="cancelEdit">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" class="bg-indigo-600 hover:bg-indigo-700 text-white" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="update">{{ __('Save Changes') }}</span>
                    <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- ========================================== -->
    <!-- Delete Confirmation Modal -->
    <!-- ========================================== -->
    <flux:modal name="delete-admission-modal" class="max-w-md p-6">
        <div class="flex flex-col items-center text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 mb-4">
                <flux:icon.exclamation-triangle class="size-8" />
            </div>
            <flux:heading size="xl" class="font-bold">{{ __('Delete Record?') }}</flux:heading>
            <flux:text class="mt-2 text-zinc-500">{{ __('Are you sure you want to delete this admission record? This action cannot be undone.') }}</flux:text>
        </div>

        <div class="mt-8 flex justify-center gap-3 w-full">
            <flux:button type="button" variant="ghost" class="w-full sm:w-auto px-6" wire:click="$dispatch('modal-close', { name: 'delete-admission-modal' })">{{ __('Cancel') }}</flux:button>
            <flux:button type="button" variant="danger" wire:click="delete" class="w-full sm:w-auto px-6">{{ __('Yes, Delete') }}</flux:button>
        </div>
    </flux:modal>

    <!-- ========================================== -->
    <!-- Import Modal -->
    <!-- ========================================== -->
    <flux:modal name="import-admission-modal" class="max-w-[40rem] p-6 sm:p-8" @close="$wire.resetImportState()">
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#eff2ff] text-[#5A45FF] dark:bg-indigo-500/20 dark:text-indigo-400">
                <flux:icon.cloud-arrow-up class="size-6" />
            </div>
            <div>
                <flux:heading size="lg" class="font-bold">{{ __('অ্যাডমিশন ডেটা ইম্পোর্ট') }}</flux:heading>
                <flux:text class="mt-1 text-sm">{{ __('Excel অথবা CSV ফাইল থেকে একসঙ্গে তথ্য যুক্ত করুন।') }}</flux:text>
            </div>
        </div>

        <form wire:submit="importData" class="space-y-6">
            @if($duplicateMessage)
                <div class="rounded-lg border border-amber-200 bg-[#FFF9E6] p-4 dark:border-amber-900/50 dark:bg-amber-500/10" role="alert">
                    <p class="text-sm font-medium text-[#92400E] dark:text-amber-500">
                        {{ $duplicateMessage }}
                    </p>
                </div>
            @endif

            <div class="rounded-xl border-2 border-dashed border-[#cbd5e1] px-6 py-8 text-center dark:border-zinc-700">
                <p class="text-[15px] font-bold text-zinc-800 dark:text-zinc-200">{{ __('Excel বা CSV ফাইল নির্বাচন করুন') }}</p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('সর্বোচ্চ ১০ MB — XLSX, XLS অথবা CSV') }}</p>

                <div class="mt-5 relative">
                    <input type="file" wire:model="file" id="file-upload" accept=".xlsx,.xls,.csv" class="peer sr-only" required>

                    <label for="file-upload" class="flex w-full cursor-pointer items-center rounded-lg border border-[#cbd5e1] text-sm shadow-sm transition-all hover:border-[#5A45FF] peer-focus-visible:ring-2 peer-focus-visible:ring-[#5A45FF] dark:border-zinc-700 overflow-hidden bg-transparent">
                        <span class="bg-[#eff2ff] px-4 py-2.5 font-semibold text-[#5A45FF] border-r border-[#cbd5e1] transition-colors dark:bg-indigo-500/20 dark:border-zinc-700 dark:text-indigo-400">
                            Choose File
                        </span>
                        <span class="flex-1 truncate px-4 text-left text-zinc-500 dark:text-zinc-400">
                            {{ $file ? $file->getClientOriginalName() : 'No file chosen' }}
                        </span>
                    </label>
                </div>

                @error('file')
                <span class="mt-2 block text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#5A45FF] px-4 py-3 text-[15px] font-bold text-white shadow-sm transition-all hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                <flux:icon.arrow-up-tray class="size-5" />
                <span wire:loading.remove wire:target="importData">{{ __('ডেটা ইম্পোর্ট করুন') }}</span>
                <span wire:loading wire:target="importData">{{ __('ইম্পোর্ট হচ্ছে...') }}</span>
            </button>
        </form>
    </flux:modal>
</div>

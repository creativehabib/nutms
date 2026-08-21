<div class="mx-auto w-full max-w-5xl p-4 sm:p-6" xmlns:flux="http://www.w3.org/1999/html">
    <flux:card class="p-0 overflow-hidden shadow-sm border border-zinc-200 dark:border-zinc-700">

        <!-- Header Section -->
        <div class="border-b border-zinc-200 bg-zinc-50/80 px-6 py-5 dark:border-zinc-700 dark:bg-zinc-800/50">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/10 shadow-sm">
                        <flux:icon.chart-bar-square class="size-6" />
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                                {{ __('Admission Summary') }}
                            </flux:heading>

                            <flux:badge color="indigo" size="sm" class="font-medium shadow-sm">
                                {{ __('Total Colleges:') }} {{ $totalColleges }}
                            </flux:badge>

                            <flux:badge color="emerald" size="sm" class="font-medium shadow-sm">
                                {{ __('Total Students (24-25):') }} {{ $globalTotalStudents }}
                            </flux:badge>
                        </div>

                        <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Select a college to view subject-wise admitted students for Session 24-25.') }}
                        </flux:text>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="shrink-0 flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">

                    <!-- Admission Manage Link Button -->
                    <flux:button :href="route('admission.manage')" wire:navigate variant="outline" size="sm" icon="table-cells" class="shadow-sm w-full sm:w-auto">
                        {{ __('Manage Admission') }}
                    </flux:button>

                    <!-- Import Trigger Button -->
                    <flux:modal.trigger name="import-admission-modal">
                        <flux:button variant="primary" size="sm" icon="cloud-arrow-up" class="shadow-sm w-full sm:w-auto">
                            {{ __('Import Data') }}
                        </flux:button>
                    </flux:modal.trigger>

                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="border-b border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:field>
                <flux:label>{{ __('Select College') }}</flux:label>
                <div wire:ignore>
                    <select
                        wire:model.live="selectedCollege"
                        data-searchable-select
                        data-placeholder="{{ __('Type to search college...') }}"
                        data-search-placeholder="{{ __('Search by college name or code') }}"
                        data-no-results-text="{{ __('No colleges found') }}"
                    >
                        <option value="">{{ __('Type to search college...') }}</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->college_code }}">
                                {{ $college->college_name }} ({{ $college->college_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </flux:field>
        </div>

        <!-- Summary Result Section -->
        @if($selectedCollege)
            @if(count($summaryData) > 0)
                <div class="bg-indigo-50/50 px-6 py-4 dark:bg-indigo-900/10 border-b border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-4 justify-between items-center">
                    <div>
                        <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Total Admitted Students') }}</span>
                        <span class="ml-2 text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($totalStudents) }}</span>
                    </div>

                    <!-- Print Button (Only active/visible when college is selected) -->
                    <flux:button variant="primary" icon="printer" size="sm" class="shadow-sm" onclick="window.open('{{ route('admission.print', ['college_code' => $selectedCollege]) }}', '_blank')">
                        {{ __('Print Data') }}
                    </flux:button>
                </div>

                <div class="overflow-x-auto p-4 sm:p-6">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Subject Name') }}</flux:table.column>
                            <flux:table.column class="text-right">{{ __('Total Admitted (Sess 24-25)') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($summaryData as $data)
                                <flux:table.row class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                                    <flux:table.cell class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $data->subject_name }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-right">
                                        <flux:badge size="sm" :color="$data->sess_24_25_total_admited > 0 ? 'emerald' : 'zinc'">
                                            {{ $data->sess_24_25_total_admited }} {{ __('Students') }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 text-zinc-500">
                    <flux:icon.document-magnifying-glass class="size-10 text-zinc-300 dark:text-zinc-600 mb-3" />
                    <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ __('No data available') }}</p>
                    <p class="text-sm mt-1">{{ __('No admission data found for this college in Session 24-25.') }}</p>
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center py-16 text-zinc-400 dark:text-zinc-500 bg-zinc-50/30 dark:bg-zinc-900/30">
                <flux:icon.cursor-arrow-rays class="size-10 mb-3 opacity-50" />
                <p class="text-sm">{{ __('Please select a college from the dropdown above to view the summary.') }}</p>
            </div>
        @endif
    </flux:card>

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

<div class="mx-auto w-full max-w-5xl p-4 sm:p-6 print:p-0 print:m-0">

    <!-- ========================================== -->
    <!-- Special print CSS (A4 Page Setup) -->
    <!-- ========================================== -->
    <style>
        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            body { background: #fff !important; color: #000 !important; -webkit-print-color-adjust: exact; }
            body * { visibility: hidden; }
            .no-print { display: none !important; }

            /* Only print area will be shown */
            #official-print-document, #official-print-document * { visibility: visible; }
            #official-print-document { position: absolute; left: 0; top: 0; width: 100%; font-family: 'Kalpurush', Arial, sans-serif; }
            /* Print Specific Overrides to ensure pure B&W rendering */
            .print-border { border-color: #374151 !important; }
            .print-bg-header { background-color: #e5e7eb !important; color: #000 !important; }
            .print-text { color: #000 !important; }

            .avoid-break { page-break-inside: avoid; }
        }
    </style>

    <!-- ========================================== -->
    <!-- Top Action Bar (Screen Only) -->
    <!-- ========================================== -->
    <div class="no-print mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Teacher Profile') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Teacher profile details and official printable record.') }}</flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button variant="subtle" :href="auth()->user()->hasRole('teacher') ? route('dashboard') : route('teachers.manage')" icon="arrow-left" wire:navigate class="hidden sm:flex">{{ __('Back') }}</flux:button>
            <flux:button type="button" variant="outline" icon="printer" onclick="window.print()">{{ __('Print') }}</flux:button>
            @can('teachers.update')
                <flux:button variant="primary" icon="pencil-square" :href="route('teachers.edit', $teacher)" wire:navigate class="shadow-sm">{{ __('Edit') }}</flux:button>
            @endcan
        </div>
    </div>

    <!-- ========================================== -->
    <!-- Document View (Screen & Print identical) -->
    <!-- ========================================== -->
    <div id="official-print-document" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm rounded-xl p-6 sm:p-10 print:border-none print:shadow-none print:p-0 print:rounded-none font-sans">

        <!-- Document Header -->
        <div class="text-center mb-8 pb-4 border-b-[3px] border-zinc-300 dark:border-zinc-700 print-border">
            <h1 class="text-2xl sm:text-3xl font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 print-text">National University, Bangladesh</h1>
            <h2 class="text-lg sm:text-xl font-semibold mt-1 text-zinc-800 dark:text-zinc-200 print-text">Teacher Information Profile</h2>
            <p class="text-sm mt-1.5 text-zinc-500 dark:text-zinc-400 print-text">Printed on: {{ date('d F Y, h:i A') }}</p>
        </div>

        <!-- 1. Personal & Professional Information -->
        <div class="mb-8 avoid-break overflow-x-auto">
            <div class="bg-zinc-100 dark:bg-zinc-800 print-bg-header border border-zinc-300 dark:border-zinc-700 print-border border-b-0 px-4 py-2.5 font-bold uppercase tracking-wider text-sm text-zinc-800 dark:text-zinc-200 print-text">
                Personal & Professional Information
            </div>
            <table class="w-full text-sm text-left border-collapse border border-zinc-300 dark:border-zinc-700 print-border min-w-[600px]">
                <tbody>
                <tr>
                    <th class="w-[20%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Teacher Name') }}</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-bold text-lg">
                        {{ $teacher->display_name }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('College Name') }}</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->college?->name ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Designation') }}</th>
                    <td class="w-[30%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->designation?->name ?: __('Not specified') }}
                    </td>
                    <th class="w-[20%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Subject') }}</th>
                    <td class="w-[30%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->subject?->name ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Teacher Level') }}</th>
                    <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->teacherLevel?->name ?: __('Not specified') }}
                    </td>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Employment Type') }}</th>
                    <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->employment?->name ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">TTIS ID</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-mono font-medium">
                        {{ $teacher->ttis_id ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Date of Birth') }}</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->birth_date?->format('d F Y') ?: __('Not specified') }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- 2. Contact Information -->
        <div class="mb-8 avoid-break overflow-x-auto">
            <div class="bg-zinc-100 dark:bg-zinc-800 print-bg-header border border-zinc-300 dark:border-zinc-700 print-border border-b-0 px-4 py-2.5 font-bold uppercase tracking-wider text-sm text-zinc-800 dark:text-zinc-200 print-text">
                Contact Information
            </div>
            <table class="w-full text-sm text-left border-collapse border border-zinc-300 dark:border-zinc-700 print-border min-w-[600px]">
                <tbody>
                <tr>
                    <th class="w-[20%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Mobile Number') }}</th>
                    <td class="w-[30%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-medium">
                        {{ $teacher->user?->mobile_no ?: __('Not specified') }}
                    </td>
                    <th class="w-[20%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Email Address') }}</th>
                    <td class="w-[30%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-indigo-600 dark:text-indigo-400 print-text font-medium">
                        {{ $teacher->user?->email ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Location') }}</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->thana?->name ?: __('Not specified') }},
                        {{ $teacher->district?->name ?: __('Not specified') }},
                        {{ $teacher->division?->name ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Present Address') }}</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->present_address ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Permanent Address') }}</th>
                    <td colspan="3" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->permanent_address ?: __('Not specified') }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- 3. Bank Information -->
        <div class="mb-8 avoid-break overflow-x-auto">
            <div class="bg-zinc-100 dark:bg-zinc-800 print-bg-header border border-zinc-300 dark:border-zinc-700 print-border border-b-0 px-4 py-2.5 font-bold uppercase tracking-wider text-sm text-zinc-800 dark:text-zinc-200 print-text">
                Bank Information
            </div>
            <table class="w-full text-sm text-left border-collapse border border-zinc-300 dark:border-zinc-700 print-border min-w-[600px]">
                <tbody>
                <tr>
                    <th class="w-[20%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Bank Name') }}</th>
                    <td class="w-[30%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->bank_name ?: __('Not specified') }}
                    </td>
                    <th class="w-[20%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Branch Name') }}</th>
                    <td class="w-[30%] border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text">
                        {{ $teacher->bank_branch_name ?: __('Not specified') }}
                    </td>
                </tr>
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Account Number') }}</th>
                    <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-mono font-medium">
                        {{ $teacher->bank_account_number ?: __('Not specified') }}
                    </td>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 print-bg-transparent text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Routing Number') }}</th>
                    <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-mono font-medium">
                        {{ $teacher->bank_routing_number ?: __('Not specified') }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- 4. Training History -->
        <div class="mb-8 avoid-break overflow-x-auto">
            <div class="bg-zinc-100 dark:bg-zinc-800 print-bg-header border border-zinc-300 dark:border-zinc-700 print-border border-b-0 px-4 py-2.5 font-bold uppercase tracking-wider text-sm text-zinc-800 dark:text-zinc-200 print-text">
                Training History
            </div>
            <table class="w-full text-sm text-left border-collapse border border-zinc-300 dark:border-zinc-700 print-border min-w-[600px]">
                <thead class="bg-zinc-50/50 dark:bg-zinc-800/30 print-bg-transparent">
                <tr>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Training Name') }}</th>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Institute') }}</th>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-700 dark:text-zinc-300 print-text font-semibold">{{ __('Duration') }}</th>
                    <th class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-700 dark:text-zinc-300 print-text font-semibold text-center">{{ __('Training Year') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($teacher->trainingTypes->isEmpty() && $teacher->otherTrainings->isEmpty())
                    <tr>
                        <td colspan="4" class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-6 text-center text-zinc-500 print-text">{{ __('No training records have been added yet.') }}</td>
                    </tr>
                @else
                    <!-- Catalog Trainings -->
                    @foreach($teacher->trainingTypes as $training)
                        <tr>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-medium">{{ $training->name }}</td>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-800 dark:text-zinc-200 print-text">{{ $training->trainingInstitute->name }}</td>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-800 dark:text-zinc-200 print-text">{{ $training->duration_value }} {{ ['hours'=>__('Hours'),'days'=>__('Days'),'weeks'=>__('Weeks'),'months'=>__('Months')][$training->duration_unit] ?? '' }}</td>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-800 dark:text-zinc-200 print-text text-center">{{ $training->pivot->training_year }}</td>
                        </tr>
                    @endforeach

                    <!-- Other Trainings -->
                    @foreach($teacher->otherTrainings as $training)
                        <tr>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-900 dark:text-zinc-100 print-text font-medium">
                                {{ $training->name }} <span class="no-print text-xs bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded ml-1 text-zinc-600 dark:text-zinc-300">{{ __('Other') }}</span>
                                <span class="hidden print:inline-block text-xs">{{ __('No training records have been added yet.') }}</span>
                            </td>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-800 dark:text-zinc-200 print-text">{{ $training->trainingInstitute?->name ?: $training->institute_name ?: __('Not specified') }}</td>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-800 dark:text-zinc-200 print-text">{{ $training->duration_value ? $training->duration_value . ' ' . (['hours'=>__('Hours'),'days'=>__('Days'),'weeks'=>__('Weeks'),'months'=>__('Months')][$training->duration_unit] ?? '') : '—' }}</td>
                            <td class="border border-zinc-300 dark:border-zinc-700 print-border px-4 py-2.5 text-zinc-800 dark:text-zinc-200 print-text text-center">{{ $training->training_year }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>

        <!-- Document Footer with Signatures -->
        <div class="mt-16 flex justify-between items-end avoid-break px-4 pb-4">
            <div class="text-center">
                <div class="w-40 sm:w-48 border-t border-zinc-400 dark:border-zinc-600 print-border pt-2 mx-auto">
                    <p class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 print-text">{{ __('Teacher Signature') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 print-text mt-0.5">Teacher's Signature</p>
                </div>
            </div>

            <div class="text-center">
                <div class="w-40 sm:w-48 border-t border-zinc-400 dark:border-zinc-600 print-border pt-2 mx-auto">
                    <p class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 print-text">{{ __('custom training') }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 print-text mt-0.5">Principal's Signature</p>
                </div>
            </div>
        </div>

    </div>

</div>

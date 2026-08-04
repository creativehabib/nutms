<div class="mx-auto w-full max-w-6xl p-4 sm:p-6 print:p-0 print:m-0">

    <!-- ========================================== -->
    <!-- স্পেশাল প্রিন্ট CSS (A4 Page Setup & Styling) -->
    <!-- ========================================== -->
    <style>
        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            body * { visibility: hidden !important; }

            #print-document, #print-document * { visibility: visible !important; }
            #print-document {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background-color: #fff !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                font-family: Arial, sans-serif;
            }

            /* Print Table Styles */
            .print-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 13px; }
            .print-table th, .print-table td { border: 1px solid #d1d5db !important; padding: 8px 12px; text-align: left; color: #000 !important; }
            .print-table th { background-color: #f3f4f6 !important; font-weight: bold; width: 35%; }

            .print-header-bg { background-color: #e5e7eb !important; padding: 8px 12px; font-weight: bold; border: 1px solid #d1d5db; border-bottom: none; text-transform: uppercase; font-size: 14px;}
            .avoid-break { page-break-inside: avoid; }
            .no-print { display: none !important; }
        }
    </style>

    <!-- ========================================== -->
    <!-- Page Header & Actions (Hidden on Print) -->
    <!-- ========================================== -->
    <div class="no-print flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between border-b border-zinc-200 pb-5 dark:border-zinc-800 mb-6">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/10 shadow-sm">
                <flux:icon.building-library class="size-7" />
            </div>
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    {{ $college->name }}
                </flux:heading>
                <div class="flex items-center gap-2 mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    <span>{{ __('College code') }}:</span>
                    <span class="font-mono font-medium text-zinc-900 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">{{ $college->code ?: __('Not provided') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button :href="auth()->user()->isAdmin() ? route('colleges.manage') : route('dashboard')" icon="arrow-left" wire:navigate class="hidden sm:flex">
                {{ auth()->user()->isAdmin() ? __('Back to list') : __('Back to dashboard') }}
            </flux:button>
            <flux:button type="button" variant="outline" icon="printer" onclick="window.print()">
                {{ __('Print Profile') }}
            </flux:button>
            <flux:button variant="primary" icon="pencil-square" :href="route('colleges.edit', $college)" wire:navigate class="shadow-sm">
                {{ __('Edit') }}
            </flux:button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- Profile Content Area (Visible on Screen & Print) -->
    <!-- ========================================== -->
    <div id="print-document" class="bg-white dark:bg-transparent">

        <!-- Print Only Header -->
        <div class="hidden print:block text-center mb-6 pb-4 border-b-2 border-black">
            <h1 class="text-2xl font-bold uppercase tracking-wider text-black">{{ $college->name }}</h1>
            <p class="text-sm mt-1 text-gray-700">College Code: <strong>{{ $college->code ?: 'N/A' }}</strong></p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 print:block print:space-y-6">

            <!-- Left Column -->
            <div class="space-y-6">

                <!-- Basic Information Table -->
                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden print:border-none print:shadow-none">
                    <div class="bg-zinc-50/80 px-4 py-3 border-b border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 print:hidden">
                        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                            <flux:icon.information-circle class="size-5 text-zinc-500" />
                            {{ __('Basic Information') }}
                        </h3>
                    </div>
                    <div class="hidden print:block print-header-bg">{{ __('Basic Information') }}</div>
                    <table class="w-full text-sm text-left print-table">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr>
                            <th class="w-2/5 sm:w-1/3 bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('College Type') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                {{ ['government'=>__('Government'),'non_government'=>__('Non-government'),'other'=>__('Other')][$college->college_type] ?? __('Not specified') }}
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Principal') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100 font-medium">{{ $college->principal_name ?: __('Not specified') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Email') }}</th>
                            <td class="px-4 py-3 text-indigo-600 dark:text-indigo-400 font-medium">{{ $college->college_email ?: __('Not specified') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Website') }}</th>
                            <td class="px-4 py-3">
                                @if($college->college_website)
                                    <a href="{{ $college->college_website }}" class="text-indigo-600 hover:underline dark:text-indigo-400" target="_blank" rel="noopener noreferrer">{{ $college->college_website }}</a>
                                @else
                                    <span class="text-zinc-500">{{ __('Not specified') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Teacher') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $college->teachers_count }} Teachers</td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Status') }}</th>
                            <td class="px-4 py-3">
                                    <span class="no-print">
                                        <flux:badge size="sm" :color="$college->is_active ? 'green' : 'zinc'">{{ $college->is_active ? __('Active') : __('Inactive') }}</flux:badge>
                                    </span>
                                <span class="hidden print:inline-block font-bold {{ $college->is_active ? 'text-green-700' : 'text-gray-500' }}">
                                        {{ $college->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Address Table -->
                <div class="avoid-break rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden print:border-none print:shadow-none">
                    <div class="bg-zinc-50/80 px-4 py-3 border-b border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 print:hidden">
                        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                            <flux:icon.map-pin class="size-5 text-zinc-500" />
                            {{ __('Address & Location') }}
                        </h3>
                    </div>
                    <div class="hidden print:block print-header-bg">{{ __('Address & Location') }}</div>
                    <table class="w-full text-sm text-left print-table">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr>
                            <th class="w-2/5 sm:w-1/3 bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Division') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $college->division?->name ?: __('Not specified') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('District') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $college->district?->name ?: __('Not specified') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Thana') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $college->thana?->name ?: __('Not specified') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Full Address') }}</th>
                            <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $college->address ?: __('Not specified') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">

                <!-- Computer Lab Table -->
                <div class="avoid-break rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden print:border-none print:shadow-none">
                    <div class="bg-zinc-50/80 px-4 py-3 border-b border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 print:hidden">
                        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                            <flux:icon.computer-desktop class="size-5 text-zinc-500" />
                            {{ __('Computer Lab') }}
                        </h3>
                    </div>
                    <div class="hidden print:block print-header-bg">{{ __('Computer Lab') }}</div>

                    <table class="w-full text-sm text-left print-table">
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @if($college->has_computer_lab)
                            <tr>
                                <th class="w-2/5 sm:w-1/3 bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">Lab Status</th>
                                <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 font-medium">Available</td>
                            </tr>
                            <tr>
                                <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">Equipment Type</th>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                    {{ ['desktop'=>__('Desktop only'),'laptop'=>__('Laptop only'),'both'=>__('Both desktop and laptop')][$college->lab_equipment_type] ?? __('Not specified') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Desktop') }}</th>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100 font-mono font-medium">{{ $college->desktop_count ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th class="bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300">{{ __('Laptop') }}</th>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100 font-mono font-medium">{{ $college->laptop_count ?? 0 }}</td>
                            </tr>
                        @elseif($college->has_computer_lab === false)
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-zinc-500">{{ __('No computer lab available') }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-zinc-500">{{ __('Computer lab details have not been added yet.') }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <!-- Academic Programs Table -->
                <div class="avoid-break rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden print:border-none print:shadow-none">
                    <div class="bg-zinc-50/80 px-4 py-3 border-b border-zinc-200 dark:bg-zinc-800/50 dark:border-zinc-700 print:hidden">
                        <h3 class="text-base font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-2">
                            <flux:icon.academic-cap class="size-5 text-zinc-500" />
                            {{ __('Academic Programs') }}
                        </h3>
                    </div>
                    <div class="hidden print:block print-header-bg">{{ __('Academic Programs') }}</div>

                    <table class="w-full text-sm text-left print-table">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-800/30 text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700 print:hidden">
                        <tr>
                            <th class="px-4 py-2.5 font-medium w-1/3 border-r border-zinc-200 dark:border-zinc-700">{{ __('Level') }}</th>
                            <th class="px-4 py-2.5 font-medium">{{ __('Programs / Subjects') }}</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($college->programs as $program)
                            <tr>
                                <th class="w-1/3 bg-zinc-50/50 px-4 py-3 font-medium text-zinc-700 dark:bg-zinc-800/30 dark:text-zinc-300 align-top border-r border-zinc-200 dark:border-zinc-700 print:border-r-0">
                                    {{ ['degree'=>__('Degree'),'honours'=>__('Honours'),'masters'=>__('Masters'),'professional'=>__('Professional'),'other'=>__('Other')][$program->level] ?? $program->level }}
                                </th>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5 no-print">
                                        @foreach($program->items ?: [$program->name] as $item)
                                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                                                    {{ $item }}
                                                </span>
                                        @endforeach
                                    </div>
                                    <div class="hidden print:block text-sm">
                                        {{ implode(', ', $program->items ?: [$program->name]) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-zinc-500">
                                    {{ __('No academic programs have been added yet.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

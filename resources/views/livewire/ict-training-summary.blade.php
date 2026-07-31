<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">

    <!-- প্রিন্ট করার জন্য বিশেষ CSS -->
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-section, #print-section * { visibility: visible; }
            #print-section { position: absolute; left: 0; top: 0; width: 100%; }
            #print-section, #print-section * { color: #000 !important; }
            #print-section, #print-section tbody, #print-section tr, #print-section td { background-color: #fff !important; }
            .no-print { display: none !important; }
            .print-table { width: 100%; border-collapse: collapse; }
            .print-table th, .print-table td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
            .print-table th { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
            .college-header { background-color: #e5e7eb !important; font-weight: bold; text-align: center; }
        }
    </style>

    <flux:card class="overflow-hidden !p-0">
        <div class="no-print flex flex-col gap-4 border-b border-zinc-200 bg-zinc-50 px-4 py-4 dark:border-zinc-700 dark:bg-zinc-800/60 sm:px-6 xl:flex-row xl:items-center xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="lg">আইসিটি ট্রেনিং সারসংক্ষেপ</flux:heading>
                <flux:text>কলেজভিত্তিক প্রশিক্ষণপ্রাপ্ত ও প্রশিক্ষণবিহীন শিক্ষকদের তালিকা</flux:text>
            </div>
            <div class="flex flex-wrap gap-2">
                <flux:button wire:click="showTab('with_ict')" :variant="$activeTab === 'with_ict' ? 'primary' : 'ghost'" icon="academic-cap">আইসিটি ট্রেনিং প্রাপ্ত শিক্ষক</flux:button>
                <flux:button wire:click="showTab('without_ict')" :variant="$activeTab === 'without_ict' ? 'primary' : 'ghost'" icon="user-minus">আইসিটি ট্রেনিং বিহীন শিক্ষক</flux:button>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <flux:button type="button" wire:click="export('{{ $activeTab }}')" wire:loading.attr="disabled" wire:target="export" variant="primary" icon="arrow-down-tray"><span wire:loading.remove wire:target="export">Excel Export</span><span wire:loading wire:target="export">Export হচ্ছে...</span></flux:button>
                <flux:button type="button" onclick="window.print()" icon="printer">তালিকা প্রিন্ট করুন</flux:button>
            </div>
        </div>

        <!-- প্রিন্ট এরিয়া -->
        <div id="print-section" class="p-6">

            <!-- আইসিটি ট্রেনিং থাকা শিক্ষকদের তালিকা -->
            @if ($activeTab === 'with_ict')
            <div>
                <flux:heading size="xl" class="mb-4 text-center">আইসিটি (ICT) ট্রেনিং প্রাপ্ত শিক্ষকদের তালিকা</flux:heading>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table border border-gray-300 dark:border-slate-600">
                        <thead class="bg-gray-800 dark:bg-slate-950 text-white print:bg-gray-200 print:text-black">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider border w-16">ক্র.নং</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border min-w-[200px]">শিক্ষকের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border">আইসিটি ট্রেনিংয়ের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border">অন্যান্য ট্রেনিংয়ের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border">ট্রেনিং ইনস্টিটিউট</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 text-sm">
                        <!-- কলেজ অনুযায়ী গ্রুপ লুপ -->
                        @forelse ($teachersByCollege as $collegeCode => $collegeTeachers)
                            <!-- কলেজ হেডার রো -->
                            <tr class="bg-gray-100 dark:bg-slate-800 print:bg-gray-200">
                                <td colspan="5" class="px-4 py-2 font-bold text-indigo-800 dark:text-indigo-300 border text-center college-header text-base">
                                    কলেজ কোড: {{ $collegeCode }} - {{ $collegeTeachers->first()->college_name ?? 'নাম উল্লেখ নেই' }}
                                </td>
                            </tr>

                            <!-- ওই কলেজের শিক্ষকদের লুপ -->
                            @php($rowNumber = 1)
                            @foreach ($collegeTeachers as $teacher)
                                <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors">
                                    <td class="px-4 py-3 text-center text-gray-900 dark:text-slate-100 border">{{ $rowNumber++ }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-800 dark:text-slate-200 border">{{ $teacher->display_name }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-slate-300 border">{{ $this->trainingDetails($teacher) }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-slate-300 border">{{ $teacher->other_training_name ?: 'উল্লেখ নেই' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-slate-400 border text-xs">{{ $this->trainingInstitutes($teacher) }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400 font-medium border">কোনো ডেটা নেই</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @else

            <!-- আইসিটি ট্রেনিং না থাকা শিক্ষকদের তালিকা -->
            <div>
                <flux:heading size="xl" class="mb-4 text-center">আইসিটি (ICT) ট্রেনিং বিহীন শিক্ষকদের তালিকা</flux:heading>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table border border-gray-300 dark:border-slate-600">
                        <thead class="bg-gray-800 dark:bg-slate-950 text-white print:bg-gray-200 print:text-black">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider border w-16">ক্র.নং</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border min-w-[200px]">শিক্ষকের নাম</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">বিষয়</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">পদবি</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">শিক্ষক স্তর</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">চাকরির ধরন</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider border">অবস্থা</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 text-sm">
                        <!-- কলেজ অনুযায়ী গ্রুপ লুপ -->
                        @forelse ($teachersByCollege as $collegeCode => $collegeTeachers)
                            <!-- কলেজ হেডার রো -->
                            <tr class="bg-gray-100 dark:bg-slate-800 print:bg-gray-200">
                                <td colspan="7" class="px-4 py-2 font-bold text-red-800 dark:text-red-300 border text-center college-header text-base">
                                    কলেজ কোড: {{ $collegeCode }} - {{ $collegeTeachers->first()->college_name ?? 'নাম উল্লেখ নেই' }}
                                </td>
                            </tr>

                            <!-- ওই কলেজের শিক্ষকদের লুপ -->
                            @php($rowNumber = 1)
                            @foreach ($collegeTeachers as $teacher)
                                <tr class="hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                    <td class="px-6 py-3 text-center text-gray-900 dark:text-slate-100 border">{{ $rowNumber++ }}</td>
                                    <td class="px-6 py-3 font-bold text-gray-800 dark:text-slate-200 border">{{ $teacher->display_name }}</td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-slate-300 border">{{ $teacher->subject ?: 'উল্লেখ নেই' }}</td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-slate-300 border">{{ $teacher->designation ?: 'উল্লেখ নেই' }}</td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-slate-300 border">{{ $teacher->teacher_level ?: 'উল্লেখ নেই' }}</td>
                                    <td class="px-6 py-3 text-gray-700 dark:text-slate-300 border">{{ $teacher->employment_type ?: 'উল্লেখ নেই' }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center border font-bold text-red-600">
                                        ট্রেনিং নেই
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400 font-medium border">কোনো ডেটা নেই</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
        <div class="no-print border-t border-gray-200 px-6 py-4 dark:border-slate-700">
            {{ $teachers->links() }}
        </div>
    </flux:card>
</div>

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

    <div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700">

        <!-- হেডার ও ট্যাব বাটন -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 no-print flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-wrap gap-2">
                <button wire:click="showTab('with_ict')"
                        class="px-4 py-2 rounded-md font-semibold text-sm transition border border-gray-300 dark:border-slate-600 shadow-sm {{ $activeTab === 'with_ict' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                    আইসিটি ট্রেনিং প্রাপ্ত শিক্ষক
                </button>

                <button wire:click="showTab('without_ict')"
                        class="px-4 py-2 rounded-md font-semibold text-sm transition border border-gray-300 dark:border-slate-600 shadow-sm {{ $activeTab === 'without_ict' ? 'bg-red-600 text-white' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                    আইসিটি ট্রেনিং বিহীন শিক্ষক
                </button>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2">
                <button type="button" wire:click="export('{{ $activeTab }}')" wire:loading.attr="disabled" wire:target="export" class="flex items-center px-4 py-2 bg-green-700 text-white rounded-md text-sm font-semibold hover:bg-green-600 disabled:opacity-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"></path></svg>
                    <span wire:loading.remove wire:target="export">Excel Export</span>
                    <span wire:loading wire:target="export">Export হচ্ছে...</span>
                </button>
                <button type="button" onclick="window.print()" class="flex items-center px-4 py-2 bg-gray-800 dark:bg-slate-950 text-white rounded-md text-sm font-semibold hover:bg-gray-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    তালিকা প্রিন্ট করুন
                </button>
            </div>
        </div>

        <!-- প্রিন্ট এরিয়া -->
        <div id="print-section" class="p-6">

            <!-- আইসিটি ট্রেনিং থাকা শিক্ষকদের তালিকা -->
            @if ($activeTab === 'with_ict')
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-slate-200 mb-4 text-center">আইসিটি (ICT) ট্রেনিং প্রাপ্ত শিক্ষকদের তালিকা</h2>
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
                                    <td class="px-4 py-3 font-bold text-gray-800 dark:text-slate-200 border">{{ $teacher->name }}</td>
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
                <h2 class="text-xl font-bold text-gray-800 dark:text-slate-200 mb-4 text-center">আইসিটি (ICT) ট্রেনিং বিহীন শিক্ষকদের তালিকা</h2>
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
                                    <td class="px-6 py-3 font-bold text-gray-800 dark:text-slate-200 border">{{ $teacher->name }}</td>
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
    </div>
</div>

<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8" x-data="{ activeTab: 'with_ict' }">

    <!-- প্রিন্ট করার জন্য বিশেষ CSS -->
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-section, #print-section * { visibility: visible; }
            #print-section { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
            .print-table { width: 100%; border-collapse: collapse; }
            .print-table th, .print-table td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
            .print-table th { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
            .college-header { background-color: #e5e7eb !important; font-weight: bold; text-align: center; }
        }
    </style>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">

        <!-- হেডার ও ট্যাব বাটন -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 no-print flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-wrap gap-2">
                <button @click="activeTab = 'with_ict'"
                        :class="activeTab === 'with_ict' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-md font-semibold text-sm transition border border-gray-300 shadow-sm">
                    আইসিটি ট্রেনিং প্রাপ্ত শিক্ষক
                </button>

                <button @click="activeTab = 'without_ict'"
                        :class="activeTab === 'without_ict' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-md font-semibold text-sm transition border border-gray-300 shadow-sm">
                    আইসিটি ট্রেনিং বিহীন শিক্ষক
                </button>
            </div>

            <!-- প্রিন্ট বাটন -->
            <button onclick="window.print()" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-semibold hover:bg-gray-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                তালিকা প্রিন্ট করুন
            </button>
        </div>

        <!-- প্রিন্ট এরিয়া -->
        <div id="print-section" class="p-6">

            <!-- আইসিটি ট্রেনিং থাকা শিক্ষকদের তালিকা -->
            <div x-show="activeTab === 'with_ict'">
                <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">আইসিটি (ICT) ট্রেনিং প্রাপ্ত শিক্ষকদের তালিকা</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table border border-gray-300">
                        <thead class="bg-gray-800 text-white print:bg-gray-200 print:text-black">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider border w-16">ক্র.নং</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border min-w-[200px]">শিক্ষকের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border">আইসিটি ট্রেনিংয়ের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border">ট্রেনিং ইনস্টিটিউট</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white text-sm">
                        <!-- কলেজ অনুযায়ী গ্রুপ লুপ -->
                        @forelse ($teachersWithIct as $collegeCode => $teachers)
                            <!-- কলেজ হেডার রো -->
                            <tr class="bg-gray-100 print:bg-gray-200">
                                <td colspan="4" class="px-4 py-2 font-bold text-indigo-800 border text-center college-header text-base">
                                    কলেজ কোড: {{ $collegeCode }} - {{ $teachers->first()->college_name ?? 'নাম উল্লেখ নেই' }}
                                </td>
                            </tr>

                            <!-- ওই কলেজের শিক্ষকদের লুপ -->
                            @foreach ($teachers as $index => $teacher)
                                <tr class="hover:bg-indigo-50 transition-colors">
                                    <td class="px-4 py-3 text-center text-gray-900 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-800 border">{{ $teacher->name }}</td>
                                    <td class="px-4 py-3 text-gray-700 border">{{ $teacher->ict_training_name }}</td>
                                    <td class="px-4 py-3 text-gray-600 border text-xs">{{ $teacher->training_institute ?? 'উল্লেখ নেই' }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 font-medium border">কোনো ডেটা নেই</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- আইসিটি ট্রেনিং না থাকা শিক্ষকদের তালিকা -->
            <div x-show="activeTab === 'without_ict'" style="display: none;">
                <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">আইসিটি (ICT) ট্রেনিং বিহীন শিক্ষকদের তালিকা</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table border border-gray-300">
                        <thead class="bg-gray-800 text-white print:bg-gray-200 print:text-black">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider border w-16">ক্র.নং</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border min-w-[200px]">শিক্ষকের নাম</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider border">অবস্থা</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white text-sm">
                        <!-- কলেজ অনুযায়ী গ্রুপ লুপ -->
                        @forelse ($teachersWithoutIct as $collegeCode => $teachers)
                            <!-- কলেজ হেডার রো -->
                            <tr class="bg-gray-100 print:bg-gray-200">
                                <td colspan="3" class="px-4 py-2 font-bold text-red-800 border text-center college-header text-base">
                                    কলেজ কোড: {{ $collegeCode }} - {{ $teachers->first()->college_name ?? 'নাম উল্লেখ নেই' }}
                                </td>
                            </tr>

                            <!-- ওই কলেজের শিক্ষকদের লুপ -->
                            @foreach ($teachers as $index => $teacher)
                                <tr class="hover:bg-red-50 transition-colors">
                                    <td class="px-6 py-3 text-center text-gray-900 border">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-3 font-bold text-gray-800 border">{{ $teacher->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-center border font-bold text-red-600">
                                        ট্রেনিং নেই
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 font-medium border">কোনো ডেটা নেই</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

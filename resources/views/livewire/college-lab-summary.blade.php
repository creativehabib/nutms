<div class="max-w-6xl mx-auto py-8 sm:px-6 lg:px-8">

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
            .print-table th, .print-table td { border: 1px solid #000; padding: 8px; text-align: left; }
            .print-table th { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
        }
    </style>

    <div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700">

        <!-- হেডার ও ট্যাব বাটন (প্রিন্টের সময় লুকানো থাকবে) -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 no-print flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex space-x-2">
                <!-- Tab 1: ল্যাব আছে -->
                <button wire:click="showTab('with_lab')"
                        class="px-4 py-2 rounded-md font-semibold text-sm transition border border-gray-300 dark:border-slate-600 {{ $activeTab === 'with_lab' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                    কম্পিউটার ল্যাব আছে
                </button>

                <!-- Tab 2: ল্যাব নেই -->
                <button wire:click="showTab('without_lab')"
                        class="px-4 py-2 rounded-md font-semibold text-sm transition border border-gray-300 dark:border-slate-600 {{ $activeTab === 'without_lab' ? 'bg-red-600 text-white' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                    কম্পিউটার ল্যাব নেই
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

        <!-- প্রিন্ট এরিয়া (Print Section) -->
        <div id="print-section" class="p-6">

            <!-- ল্যাব থাকা কলেজগুলোর তালিকা (Tab 1 Content) -->
            @if ($activeTab === 'with_lab')
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-slate-200 mb-4 text-center">যেসব কলেজে কম্পিউটার ল্যাব আছে</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table border border-gray-300 dark:border-slate-600">
                        <thead class="bg-gray-800 dark:bg-slate-950 text-white print:bg-gray-200 print:text-black">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">ক্র.নং</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">কলেজ কোড</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">কলেজের নাম</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider border">কম্পিউটারের সংখ্যা</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 text-sm">
                        @forelse ($colleges as $college)
                            <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap text-gray-900 dark:text-slate-100 border">{{ $colleges->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3 whitespace-nowrap font-bold text-gray-900 dark:text-slate-100 border">{{ $college->college_code }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-gray-700 dark:text-slate-300 font-medium border">{{ $college->college_name ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-center border font-bold text-green-700 dark:text-green-300">
                                    {{ $college->total_computers ?? 0 }} টি
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400 font-medium border">কোনো ডেটা নেই</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @else

            <!-- ল্যাব না থাকা কলেজগুলোর তালিকা (Tab 2 Content) -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-slate-200 mb-4 text-center">যেসব কলেজে কোনো কম্পিউটার ল্যাব নেই</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 print-table border border-gray-300 dark:border-slate-600">
                        <thead class="bg-gray-800 dark:bg-slate-950 text-white print:bg-gray-200 print:text-black">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">ক্র.নং</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">কলেজ কোড</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider border">কলেজের নাম</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider border">অবস্থা</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 text-sm">
                        @forelse ($colleges as $college)
                            <tr class="hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap text-gray-900 dark:text-slate-100 border">{{ $colleges->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3 whitespace-nowrap font-bold text-gray-900 dark:text-slate-100 border">{{ $college->college_code }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-gray-700 dark:text-slate-300 font-medium border">{{ $college->college_name ?? '-' }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-center border font-bold text-red-600 dark:text-red-300">
                                    ল্যাব নেই
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400 font-medium border">কোনো ডেটা নেই</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
        <div class="no-print border-t border-gray-200 px-6 py-4 dark:border-slate-700">
            {{ $colleges->links() }}
        </div>
    </div>
</div>

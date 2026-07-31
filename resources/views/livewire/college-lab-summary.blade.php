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

    <flux:card class="overflow-hidden !p-0">
        <div class="no-print flex flex-col gap-4 border-b border-zinc-200 bg-zinc-50 px-4 py-4 dark:border-zinc-700 dark:bg-zinc-800/60 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <flux:heading size="lg">কম্পিউটার ল্যাব সারসংক্ষেপ</flux:heading>
                <flux:text>সক্রিয় কলেজগুলোর ল্যাব সুবিধার হালনাগাদ তালিকা</flux:text>
            </div>
            <div class="flex flex-wrap gap-2">
                <flux:button wire:click="showTab('with_lab')" :variant="$activeTab === 'with_lab' ? 'primary' : 'ghost'" icon="computer-desktop">কম্পিউটার ল্যাব আছে</flux:button>
                <flux:button wire:click="showTab('without_lab')" :variant="$activeTab === 'without_lab' ? 'primary' : 'ghost'" icon="x-circle">কম্পিউটার ল্যাব নেই</flux:button>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <flux:button type="button" wire:click="export('{{ $activeTab }}')" wire:loading.attr="disabled" wire:target="export" variant="primary" icon="arrow-down-tray"><span wire:loading.remove wire:target="export">Excel Export</span><span wire:loading wire:target="export">Export হচ্ছে...</span></flux:button>
                <flux:button type="button" onclick="window.print()" icon="printer">তালিকা প্রিন্ট করুন</flux:button>
            </div>
        </div>

        <!-- প্রিন্ট এরিয়া (Print Section) -->
        <div id="print-section" class="p-6">

            <!-- ল্যাব থাকা কলেজগুলোর তালিকা (Tab 1 Content) -->
            @if ($activeTab === 'with_lab')
            <div>
                <flux:heading size="xl" class="mb-4 text-center">যেসব কলেজে কম্পিউটার ল্যাব আছে</flux:heading>
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
                                <td class="px-6 py-3 whitespace-nowrap font-bold text-gray-900 dark:text-slate-100 border">{{ $college->code }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-gray-700 dark:text-slate-300 font-medium border">{{ $college->name }}</td>
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
                <flux:heading size="xl" class="mb-4 text-center">যেসব কলেজে কোনো কম্পিউটার ল্যাব নেই</flux:heading>
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
                                <td class="px-6 py-3 whitespace-nowrap font-bold text-gray-900 dark:text-slate-100 border">{{ $college->code }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-gray-700 dark:text-slate-300 font-medium border">{{ $college->name }}</td>
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
    </flux:card>
</div>

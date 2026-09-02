<div class="max-w-2xl mx-auto my-10 bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-800 p-8 transition-colors duration-300">

    <!-- Top Heading -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-400 leading-snug">
            ২০২৬ সালে আপনার গ্রেডে বেতন কত <br> বাড়বে?
        </h2>
        <p class="text-xs pt-1">গ্রেড অনুযায়ী পার্সেন্টেজ নির্ধারণ (১-৯ গ্রেডে ৪০%, ১০-২০ গ্রেডে ৫০%)</p>
    </div>

    <!-- Input Form using Flux UI -->
    <form wire:submit.prevent="calculate" class="space-y-6">

        <!-- Grade Selection -->
        <flux:select wire:model.live="grade" label="গ্রেড নির্বাচন করুন (২০১৫ স্কেল):" placeholder="গ্রেড বেছে নিন">
            @for($i = 1; $i <= 20; $i++)
                <flux:select.option value="{{ $i }}">গ্রেড {{ $i }}</flux:select.option>
            @endfor
        </flux:select>

        <!-- Step Selection -->
        <flux:select wire:model.live="step" label="বেতনের ধাপ (Step):" :disabled="empty($this->steps)">
            @if(empty($this->steps))
                <flux:select.option value="" disabled>আগে গ্রেড বেছে নিন</flux:select.option>
            @else
                <flux:select.option value="" disabled>ধাপ বেছে নিন</flux:select.option>
                @foreach($this->steps as $index => $basicAmount)
                    <flux:select.option value="{{ $basicAmount }}">
                        ধাপ {{ $index + 1 }} ({{ number_format($basicAmount) }}/-)
                    </flux:select.option>
                @endforeach
            @endif
        </flux:select>

        <!-- Location Selection -->
        <flux:select wire:model="location" label="বর্তমান কর্মস্থল:">
            <flux:select.option value="1">ঢাকা সিটি কর্পোরেশন</flux:select.option>
            <flux:select.option value="2">অন্যান্য বিভাগীয় শহর/গাজীপুর/সাভার</flux:select.option>
            <flux:select.option value="3">অন্যান্য স্থান (জেলা/উপজেলা)</flux:select.option>
        </flux:select>

        <!-- Submit Button -->
        <div class="pt-2">
            <flux:button type="submit" class="w-full !bg-emerald-600 hover:!bg-emerald-700 dark:!bg-emerald-500 dark:hover:!bg-emerald-600 !text-white text-lg py-5">
                হিসাব করুন
            </flux:button>
        </div>
    </form>

    <!-- Detailed Report Section -->
    @if($result)
        <div class="mt-10 border border-gray-200 dark:border-slate-700 p-8 bg-white dark:bg-slate-900/50 rounded-xl transition-colors duration-300">

            <div class="text-center mb-6">
                <h3 class="text-[22px] font-bold text-gray-800 dark:text-slate-100 mb-2">বেতন নির্ধারণী প্রতিবেদন – ২০২৬</h3>
                <p class="text-[17px] font-medium text-gray-700 dark:text-slate-400">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</p>
            </div>

            <div class="text-sm font-medium text-gray-800 dark:text-slate-200 mb-4 border-b border-blue-700 dark:border-blue-500 pb-3">
                গ্রেড: <span class="border-b border-dotted border-gray-500 dark:border-slate-400 pb-0.5">{{ $result['grade'] }}</span> |
                বর্তমান মূল বেতন (২০১৫): <span class="border-b border-dotted border-gray-500 dark:border-slate-400 pb-0.5">{{ number_format($result['current_basic']) }}</span> টাকা।
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-blue-700 dark:border-blue-500 text-blue-800 dark:text-blue-200 text-sm">
                    <thead>
                    <tr class="bg-blue-50/50 dark:bg-blue-900/30">
                        <th class="border border-blue-700 dark:border-blue-500 p-3 text-left font-bold w-1/3">ধাপ</th>
                        <th class="border border-blue-700 dark:border-blue-500 p-3 text-left font-bold w-1/3">বিবরণ</th>
                        <th class="border border-blue-700 dark:border-blue-500 p-3 text-left font-bold w-1/3">পরিমাণ</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-700 dark:divide-blue-500">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">বর্তমান মূল বেতন (পুরনো)</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">২০১৫ স্কেলে বর্তমান বেসিক</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['current_basic']) }} টাকা</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">অর্জিত ইনক্রিমেন্ট</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['current_basic']) }} - {{ number_format($result['old_base']) }}</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['earned_inc']) }} টাকা</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">সমন্বিত বেসিক</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['new_base']) }} + {{ number_format($result['earned_inc']) }}</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['adjusted_basic']) }} টাকা</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">পরবর্তী উচ্চতর ধাপ</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">নতুন স্কেলের ধাপ-{{ $result['next_step_index'] }}</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['next_step_new']) }} টাকা</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">বেসিকের পার্থক্য</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['next_step_new']) }} - {{ number_format($result['current_basic']) }}</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['basic_diff']) }} টাকা</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">{{ $this->toBengali($result['percentage']) }}% নগদ সুবিধা</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">
                            @if($result['percentage'] == 50)
                                {{ number_format($result['basic_diff']) }} ÷ 2
                            @else
                                {{ number_format($result['basic_diff']) }} × ৪০%
                            @endif
                        </td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['benefit']) }} টাকা</td>
                    </tr>
                    <tr class="font-bold bg-blue-100/50 dark:bg-blue-900/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">🎯 চূড়ান্ত নতুন মূল বেতন</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['current_basic']) }} + {{ number_format($result['benefit']) }}</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 text-emerald-700 dark:text-emerald-400">{{ number_format($result['final_basic']) }} টাকা</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">বাড়ি ভাড়া ভাতা</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">নতুন মূল বেতনের উপর ভিত্তি করে</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['house_rent']) }} টাকা</td>
                    </tr>
                    <tr class="border-b border-blue-700 dark:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">চিকিৎসা ভাতা</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">স্থায়ী নির্ধারিত পরিমাণ</td>
                        <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['medical']) }} টাকা</td>
                    </tr>
                    @if($result['tiffin'] > 0)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="border-x border-blue-700 dark:border-blue-500 p-2.5 font-bold">টিফিন ভাতা</td>
                            <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">১১-২০ গ্রেডের জন্য নির্ধারিত</td>
                            <td class="border-x border-blue-700 dark:border-blue-500 p-2.5">{{ number_format($result['tiffin']) }} টাকা</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <div class="text-right mt-6 font-bold text-gray-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-800 p-4 rounded-lg inline-block float-right border border-slate-200 dark:border-slate-700">
                <span class="text-[17px]">সর্বমোট মাসিক গ্রস বেতন:</span>
                <span class="text-2xl ml-2 text-emerald-600 dark:text-emerald-400">{{ number_format($result['total']) }} টাকা</span>
            </div>
            <div class="clear-both">
                <p class="text-xs pt-2.5 text-center">এটি পে কমিশনের সুপারিশ এবং আপনার নতুন লজিক অনুযায়ী তৈরী করা হয়েছে। সরকারি গেজেট প্রকাশের উপর কম বা বেশি হতে পারে।</p>
            </div>
        </div>
    @endif
</div>

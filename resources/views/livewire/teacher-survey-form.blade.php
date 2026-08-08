<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 font-sans">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300">

        <!-- Header -->
        <div class="bg-indigo-600 dark:bg-indigo-700 px-6 py-8 text-center transition-colors duration-300">
            <h2 class="text-xl sm:text-2xl font-bold text-white leading-snug">জাতীয় বিশ্ববিদ্যালয়ের প্রথম বর্ষের আইসিটি কোর্স বিষয়ক শিক্ষক মতামত জরিপ</h2>
        </div>

        <div class="p-6 sm:p-10">

            {{-- যদি আগে সাবমিট করে থাকে বা মাত্র সাবমিট করল --}}
            @if($hasSubmitted)
                <div class="text-center py-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-500/20 mb-4 transition-colors duration-300">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 transition-colors duration-300">ধন্যবাদ!</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-lg transition-colors duration-300">
                        {{ $successMessage ?: 'আপনি ইতোমধ্যে এই জরিপে অংশগ্রহণ করেছেন।' }}
                    </p>
                </div>
            @else

                {{-- জরিপ ফর্ম --}}
                <form wire:submit.prevent="submit" class="space-y-10">

                    @php
                        $scaleOptions = ["সম্পূর্ণ অসম্মত", "অসম্মত", "নিরপেক্ষ", "সম্মত", "সম্পূর্ণ সম্মত"];
                    @endphp

                        <!-- Q1 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ১. প্রথম বর্ষের আইসিটি কোর্স পরিচালনার জন্য আপনি কীভাবে নিজেকে প্রস্তুত করেছেন?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="space-y-3 sm:pl-2">
                            @foreach([
                                "কোর/মাস্টার ট্রেইনার প্রশিক্ষণের মাধ্যমে (জাতীয় বিশ্ববিদ্যালয় ও ইউনিসেফ)।",
                                "কলেজ আয়োজিত ইন-হাউজ প্রশিক্ষণের মাধ্যমে।",
                                "পূর্ববর্তী আইসিটি প্রশিক্ষণের মাধ্যমে।",
                                "অনলাইন কোর্স (মুক্তপাঠ/ইউনিসেফের P2E) ও অন্যান্য শিক্ষাসামগ্রী ব্যবহার করে।",
                                "পাঠ্যক্রম, শিক্ষক নির্দেশিকা ও অন্যান্য উপকরণ স্ব-অধ্যয়নের মাধ্যমে।",
                                "সহকর্মীদের সহযোগিতায়।",
                                "এখনো প্রস্তুতি নেওয়ার সুযোগ পাইনি।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="q1" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex flex-col sm:flex-row sm:items-center mt-2 group">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="q1" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                                </div>
                                <input type="text" wire:model="q1_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 transition-colors" placeholder="লিখুন...">
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q2 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ২. আপনি প্রধানত কীভাবে আইসিটি কোর্সটি পরিচালনা করেছেন?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="space-y-3 sm:pl-2">
                            @foreach([
                                "শুধুমাত্র শ্রেণিকক্ষে পাঠদান করেছি।",
                                "শ্রেণিকক্ষের পাঠদানের পাশাপাশি ব্যবহারিক/ল্যাব ক্লাস নিয়েছি।",
                                "শ্রেণিকক্ষের পাঠদানের পাশাপাশি অনলাইন কোর্স ব্যবহার করেছি।",
                                "শ্রেণিকক্ষ, ব্যবহারিক ক্লাস ও অনলাইন কোর্স—সবগুলো সমন্বয় করেছি।",
                                "এখনো কোর্স পরিচালনার সুযোগ পাইনি।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="q2" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q3 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ৩. আইসিটি কোর্স পরিচালনার সময় আপনি কোন কোন চ্যালেঞ্জের সম্মুখীন হয়েছেন?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="space-y-3 sm:pl-2">
                            @foreach([
                                "কলেজে পর্যাপ্ত আইসিটি শিক্ষক নেই।",
                                "আমি কোনো আনুষ্ঠানিক প্রশিক্ষণ পাইনি।",
                                "কম্পিউটার ল্যাবের সুবিধা সীমিত।",
                                "পর্যাপ্ত কম্পিউটার বা ইন্টারনেট সুবিধা নেই।",
                                "শ্রেণিকক্ষে শিক্ষার্থীর সংখ্যা বেশি।",
                                "ব্যবহারিক ক্লাসের জন্য পর্যাপ্ত সময় নেই।",
                                "শিক্ষার্থীরা অনলাইন কোর্স সম্পর্কে অবগত নয়।",
                                "পর্যাপ্ত শিক্ষাসামগ্রী বা রিসোর্সের অভাব।",
                                "উল্লেখযোগ্য কোনো চ্যালেঞ্জ নেই।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="q3" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex flex-col sm:flex-row sm:items-center mt-2 group">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="q3" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                                </div>
                                <input type="text" wire:model="q3_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 transition-colors">
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q4 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 block transition-colors duration-300">
                            ৪. অনলাইন আইসিটি কোর্স (মুক্তপাঠ/P2E) শিক্ষার্থীদের পরীক্ষার প্রস্তুতিতে কার্যকর সহায়ক হিসেবে কাজ করেছে।
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(৫-ধাপের মূল্যায়ন স্কেল)</span>
                        </label>
                        <div class="flex flex-wrap gap-3 sm:gap-4 sm:pl-2">
                            @foreach($scaleOptions as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                                    <input type="radio" wire:model="q4" value="{{ $opt }}" class="border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-2.5 text-sm sm:text-base text-slate-700 dark:text-slate-200">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q5 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 block transition-colors duration-300">
                            ৫. আমি প্রথম বর্ষের আইসিটি কোর্সটি কার্যকরভাবে পরিচালনা করতে আত্মবিশ্বাসী।
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(৫-ধাপের মূল্যায়ন স্কেল)</span>
                        </label>
                        <div class="flex flex-wrap gap-3 sm:gap-4 sm:pl-2">
                            @foreach($scaleOptions as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                                    <input type="radio" wire:model="q5" value="{{ $opt }}" class="border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-2.5 text-sm sm:text-base text-slate-700 dark:text-slate-200">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q6 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 block transition-colors duration-300">
                            ৬. সামগ্রিকভাবে, প্রথম বর্ষের আইসিটি কোর্সের পাঠ্যক্রম, অনলাইন কোর্স এবং শিক্ষাসামগ্রী নিয়ে আপনি কতটা সন্তুষ্ট?
                        </label>
                        <div class="flex flex-wrap gap-3 sm:gap-4 sm:pl-2">
                            @foreach(["অত্যন্ত সন্তুষ্ট", "সন্তুষ্ট", "নিরপেক্ষ", "অসন্তুষ্ট", "অত্যন্ত অসন্তুষ্ট"] as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                                    <input type="radio" wire:model="q6" value="{{ $opt }}" class="border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-2.5 text-sm sm:text-base text-slate-700 dark:text-slate-200">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q7 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ৭. আইসিটি কোর্সটি আরও কার্যকরভাবে পরিচালনার জন্য আপনার মতে কোন ধরনের অতিরিক্ত সহায়তা সবচেয়ে বেশি প্রয়োজন?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:pl-2">
                            @foreach([
                                "ইন-হাউজ শিক্ষক প্রশিক্ষণ।",
                                "রিফ্রেশার প্রশিক্ষণ।",
                                "ভিডিও টিউটোরিয়াল ও ডেমোনস্ট্রেশন।",
                                "উন্নত কম্পিউটার ল্যাব সুবিধা।",
                                "উন্নত ইন্টারনেট সংযোগ।",
                                "পরীক্ষার প্রস্তুতির জন্য অতিরিক্ত শিক্ষাসামগ্রী।",
                                "অনলাইন কোর্স ব্যবহারের নির্দেশিকা।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="q7" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-snug">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- Q8 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ৮. আইসিটি কোর্স সম্পর্কিত তথ্য ও আপডেট আপনি কোন কোন মাধ্যমে পেতে চান?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:pl-2 mb-3">
                            @foreach([
                                "জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল ওয়েবসাইট।",
                                "জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল ফেসবুক পেজ।",
                                "কলেজ প্রশাসনের মাধ্যমে।",
                                "বিভাগ/শিক্ষকদের ফেসবুক বা হোয়াটসঅ্যাপ গ্রুপ।",
                                "ই-মেইল।",
                                "এসএমএস (SMS)",
                                "শিক্ষক প্রশিক্ষণ কর্মশালা।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="q8" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-snug">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <label class="flex flex-col sm:flex-row sm:items-center mt-3 sm:pl-2 w-full group">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="q8" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-500/50 transition-colors">
                                <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                            </div>
                            <input type="text" wire:model="q8_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 transition-colors">
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex justify-center sm:justify-end">
                        <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold py-3.5 px-10 rounded-xl shadow-lg shadow-indigo-600/30 dark:shadow-indigo-900/50 transition-all duration-200 ease-in-out flex justify-center items-center disabled:opacity-70">
                            <span>জরিপ জমা দিন</span>
                            <div wire:loading wire:target="submit" class="ml-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </button>
                    </div>
                </form>

            @endif
        </div>
    </div>
</div>

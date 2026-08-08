<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 font-sans">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300">

        <!-- Header -->
        <div class="bg-teal-600 dark:bg-teal-700 px-6 py-8 text-center transition-colors duration-300">
            <h2 class="text-xl sm:text-2xl font-bold text-white leading-snug">জাতীয় বিশ্ববিদ্যালয়ের প্রথম বর্ষের আইসিটি কোর্স বিষয়ক শিক্ষার্থী মতামত জরিপ</h2>
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

                        <!-- SQ1 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ১. প্রথম বর্ষের আইসিটি কোর্স সম্পন্ন করতে এবং পরীক্ষার প্রস্তুতির জন্য আপনি নিচের কোন কোন পদ্ধতি অনুসরণ করেছেন?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="space-y-3 sm:pl-2">
                            @foreach([
                                "আমি নিয়মিত শ্রেণিকক্ষে অংশগ্রহণ করেছি।",
                                "অনলাইন কোর্স (মুক্তপাঠ/ইউনিসেফের P2E) সম্পন্ন করেছি।",
                                "গাইডবই বা সহায়ক বই ব্যবহার করেছি।",
                                "নিজ উদ্যোগে পড়াশোনা করেছি।",
                                "কোচিং করেছি।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="sq1" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex flex-col sm:flex-row sm:items-center mt-2 group">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="sq1" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                                </div>
                                <input type="text" wire:model="sq1_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-teal-500 dark:focus:border-teal-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 transition-colors">
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- SQ2 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ২. আইসিটি কোর্সের ব্যবহারিক (Practical) ক্লাস আপনি কীভাবে সম্পন্ন করেছেন?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="space-y-3 sm:pl-2">
                            @foreach([
                                "কলেজের কম্পিউটার ল্যাবে।",
                                "আমাদের কলেজে কম্পিউটার ল্যাব নেই।",
                                "কলেজ ল্যাব থাকলেও ব্যবহার করার সুযোগ পাইনি।",
                                "নিজের বা অন্য কারও মোবাইল ফোন ব্যবহার করে।",
                                "নিজের বা অন্য কারও কম্পিউটার ব্যবহার করে।",
                                "এলাকার কম্পিউটার প্রশিক্ষণ কেন্দ্র/সাইবার ক্যাফেতে।",
                                "আমি কোনো ব্যবহারিক ক্লাসে অংশগ্রহণ করিনি।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="sq2" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex flex-col sm:flex-row sm:items-center mt-2 group">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="sq2" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                                </div>
                                <input type="text" wire:model="sq2_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-teal-500 dark:focus:border-teal-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 transition-colors">
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- SQ3 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 block transition-colors duration-300">
                            ৩. প্রথম বর্ষের আইসিটি কোর্সটি আমার ডিজিটাল জ্ঞান ও ব্যবহারিক দক্ষতা বৃদ্ধি করেছে।
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(৫-ধাপের মূল্যায়ন স্কেল)</span>
                        </label>
                        <div class="flex flex-wrap gap-3 sm:gap-4 sm:pl-2">
                            @foreach($scaleOptions as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl hover:border-teal-400 dark:hover:border-teal-500 transition-colors">
                                    <input type="radio" wire:model="sq3" value="{{ $opt }}" class="border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-2.5 text-sm sm:text-base text-slate-700 dark:text-slate-200">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- SQ4 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 block transition-colors duration-300">
                            ৪. এই কোর্সটি আমার ভবিষ্যৎ কর্মজীবনের জন্য সহায়ক হবে।
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(৫-ধাপের মূল্যায়ন স্কেল)</span>
                        </label>
                        <div class="flex flex-wrap gap-3 sm:gap-4 sm:pl-2">
                            @foreach($scaleOptions as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl hover:border-teal-400 dark:hover:border-teal-500 transition-colors">
                                    <input type="radio" wire:model="sq4" value="{{ $opt }}" class="border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-2.5 text-sm sm:text-base text-slate-700 dark:text-slate-200">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- SQ5 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ৫. প্রথম বর্ষের আইসিটি কোর্স সম্পন্ন করতে আপনার কী কী অসুবিধা হয়েছে?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:pl-2">
                            @foreach([
                                "কলেজে পর্যাপ্ত আইসিটি শিক্ষক ছিল না।",
                                "নিয়মিত ক্লাস অনুষ্ঠিত হয়নি।",
                                "কম্পিউটার ল্যাবের সুবিধা ছিল না।",
                                "সময়মতো কোর্স সম্পর্কে জানতে পারিনি।",
                                "অনলাইন কোর্স সম্পর্কে জানতাম না।",
                                "কোর্সের বিষয়বস্তু আমার কাছে কঠিন মনে হয়েছে।",
                                "ইন্টারনেট বা ডিভাইসের সমস্যা ছিল।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="sq5" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors leading-snug">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <label class="flex flex-col sm:flex-row sm:items-center mt-3 sm:pl-2 w-full group">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="sq5" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                            </div>
                            <input type="text" wire:model="sq5_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-teal-500 dark:focus:border-teal-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 transition-colors">
                        </label>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- SQ6 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ৬. আপনার মতে, প্রথম বর্ষের আইসিটি কোর্স আরও কার্যকর করার জন্য কোন বিষয়গুলো সবচেয়ে গুরুত্বপূর্ণ?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="space-y-3 sm:pl-2">
                            @foreach([
                                "পর্যাপ্ত আইসিটি শিক্ষক নিয়োগ।",
                                "নিয়মিত শ্রেণিকক্ষের পাঠদান নিশ্চিত করা।",
                                "সকল শিক্ষার্থীকে অনলাইন কোর্স সম্পর্কে অবহিত করা।",
                                "পর্যাপ্ত কম্পিউটার ও আধুনিক ল্যাব সুবিধা নিশ্চিত করা।",
                                "ব্যবহারিক ক্লাস ও অ্যাসাইনমেন্টের সংখ্যা বৃদ্ধি করা।",
                                "কলেজে ইন্টারনেট সুবিধা উন্নত করা।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="sq6" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex flex-col sm:flex-row sm:items-center mt-2 group">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="sq6" value="other" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 mr-3 text-slate-700 dark:text-slate-300 whitespace-nowrap group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">অন্যান্য (উল্লেখ করুন):</span>
                                </div>
                                <input type="text" wire:model="sq6_other_text" class="mt-2 sm:mt-0 flex-1 border-b border-slate-300 dark:border-slate-600 focus:border-teal-500 dark:focus:border-teal-400 focus:ring-0 bg-transparent px-2 py-1 outline-none text-slate-800 dark:text-slate-200 transition-colors">
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-200 dark:border-slate-800">

                    <!-- SQ7 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3 block transition-colors duration-300">
                            ৭. আইসিটি কোর্স সম্পর্কিত তথ্য ও আপডেট আপনি প্রধানত কোন কোন মাধ্যমে পেতে চান?
                            <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline mt-1 sm:mt-0">(একাধিক উত্তর নির্বাচন করা যাবে)</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:pl-2">
                            @foreach([
                                "জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল ওয়েবসাইট।",
                                "জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল ফেসবুক পেজ।",
                                "কলেজের অফিসিয়াল ফেসবুক পেজ।",
                                "বিভাগ/ডিপার্টমেন্টের ফেসবুক বা হোয়াটসঅ্যাপ গ্রুপ।",
                                "এসএমএস (SMS)",
                                "ই-মেইল।",
                                "কোর্স শিক্ষক।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="sq7" value="{{ $opt }}" class="mt-1 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-teal-600 focus:ring-teal-500 dark:focus:ring-teal-500/50 transition-colors">
                                    <span class="ml-3 text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors leading-snug">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex justify-center sm:justify-end">
                        <button type="submit" class="w-full sm:w-auto bg-teal-600 hover:bg-teal-700 dark:bg-teal-600 dark:hover:bg-teal-500 text-white font-bold py-3.5 px-10 rounded-xl shadow-lg shadow-teal-600/30 dark:shadow-teal-900/50 transition-all duration-200 ease-in-out flex justify-center items-center disabled:opacity-70">
                            <span>জরিপ জমা দিন</span>
                            <div wire:loading wire:target="submit" class="ml-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </button>
                    </div>
                </form>

            @endif
        </div>
    </div>
</div>

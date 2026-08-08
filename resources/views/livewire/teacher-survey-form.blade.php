<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <!-- Header -->
        <div class="bg-indigo-600 px-6 py-8 text-center">
            <h2 class="text-2xl font-bold text-white">জাতীয় বিশ্ববিদ্যালয়ের প্রথম বর্ষের আইসিটি কোর্স বিষয়ক শিক্ষক মতামত জরিপ</h2>
        </div>

        <div class="p-6 sm:p-10">

            {{-- যদি আগে সাবমিট করে থাকে বা মাত্র সাবমিট করল --}}
            @if($hasSubmitted)
                <div class="text-center py-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">ধন্যবাদ!</h3>
                    <p class="text-slate-600 text-lg">
                        {{ $successMessage ?: 'আপনি ইতোমধ্যে এই জরিপে অংশগ্রহণ করেছেন।' }}
                    </p>
                    <a href="{{ route('admin.survey.report') }}" class="mt-6 inline-block bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-6 py-2 rounded-lg font-medium transition-colors">
                        ড্যাশবোর্ডে ফিরে যান
                    </a>
                </div>
            @else

                {{-- জরিপ ফর্ম --}}
                <form wire:submit.prevent="submit" class="space-y-10">

                    @php
                        $scaleOptions = ["সম্পূর্ণ অসম্মত", "অসম্মত", "নিরপেক্ষ", "সম্মত", "সম্পূর্ণ সম্মত"];
                    @endphp

                        <!-- Q1 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-3 block">১. প্রথম বর্ষের আইসিটি কোর্স পরিচালনার জন্য আপনি কীভাবে নিজেকে প্রস্তুত করেছেন? <span class="text-sm font-normal text-slate-500">(একাধিক উত্তর নির্বাচন করা যাবে)</span></label>
                        <div class="space-y-3 pl-2">
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
                                    <input type="checkbox" wire:model="q1" value="{{ $opt }}" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                    <span class="ml-3 text-slate-700 group-hover:text-slate-900">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex items-center mt-2">
                                <input type="checkbox" wire:model="q1" value="other" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 mr-3 text-slate-700">অন্যান্য (উল্লেখ করুন):</span>
                                <input type="text" wire:model="q1_other_text" class="flex-1 border-b border-slate-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-2 py-1 text-slate-800 outline-none transition-colors" placeholder="লিখুন...">
                            </label>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-3 block">২. আপনি প্রধানত কীভাবে আইসিটি কোর্সটি পরিচালনা করেছেন? <span class="text-sm font-normal text-slate-500">(একাধিক উত্তর নির্বাচন করা যাবে)</span></label>
                        <div class="space-y-3 pl-2">
                            @foreach([
                                "শুধুমাত্র শ্রেণিকক্ষে পাঠদান করেছি।",
                                "শ্রেণিকক্ষের পাঠদানের পাশাপাশি ব্যবহারিক/ল্যাব ক্লাস নিয়েছি।",
                                "শ্রেণিকক্ষের পাঠদানের পাশাপাশি অনলাইন কোর্স ব্যবহার করেছি।",
                                "শ্রেণিকক্ষ, ব্যবহারিক ক্লাস ও অনলাইন কোর্স—সবগুলো সমন্বয় করেছি।",
                                "এখনো কোর্স পরিচালনার সুযোগ পাইনি।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" wire:model="q2" value="{{ $opt }}" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-3 block">৩. আইসিটি কোর্স পরিচালনার সময় আপনি কোন কোন চ্যালেঞ্জের সম্মুখীন হয়েছেন? <span class="text-sm font-normal text-slate-500">(একাধিক উত্তর নির্বাচন করা যাবে)</span></label>
                        <div class="space-y-3 pl-2">
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
                                    <input type="checkbox" wire:model="q3" value="{{ $opt }}" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                            <label class="flex items-center mt-2">
                                <input type="checkbox" wire:model="q3" value="other" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 mr-3 text-slate-700">অন্যান্য (উল্লেখ করুন):</span>
                                <input type="text" wire:model="q3_other_text" class="flex-1 border-b border-slate-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-2 py-1 outline-none">
                            </label>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-4 block">৪. অনলাইন আইসিটি কোর্স (মুক্তপাঠ/P2E) শিক্ষার্থীদের পরীক্ষার প্রস্তুতিতে কার্যকর সহায়ক হিসেবে কাজ করেছে। <span class="text-sm font-normal text-slate-500">(৫-ধাপের মূল্যায়ন স্কেল)</span></label>
                        <div class="flex flex-wrap gap-4 pl-2">
                            @foreach($scaleOptions as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg hover:border-indigo-300 transition-colors">
                                    <input type="radio" wire:model="q4" value="{{ $opt }}" class="border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Q5 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-4 block">৫. আমি প্রথম বর্ষের আইসিটি কোর্সটি কার্যকরভাবে পরিচালনা করতে আত্মবিশ্বাসী। <span class="text-sm font-normal text-slate-500">(৫-ধাপের মূল্যায়ন স্কেল)</span></label>
                        <div class="flex flex-wrap gap-4 pl-2">
                            @foreach($scaleOptions as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg hover:border-indigo-300 transition-colors">
                                    <input type="radio" wire:model="q5" value="{{ $opt }}" class="border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Q6 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-4 block">৬. সামগ্রিকভাবে, প্রথম বর্ষের আইসিটি কোর্সের পাঠ্যক্রম, অনলাইন কোর্স এবং শিক্ষাসামগ্রী নিয়ে আপনি কতটা সন্তুষ্ট?</label>
                        <div class="flex flex-wrap gap-4 pl-2">
                            @foreach(["অত্যন্ত সন্তুষ্ট", "সন্তুষ্ট", "নিরপেক্ষ", "অসন্তুষ্ট", "অত্যন্ত অসন্তুষ্ট"] as $opt)
                                <label class="flex items-center cursor-pointer bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg hover:border-indigo-300 transition-colors">
                                    <input type="radio" wire:model="q6" value="{{ $opt }}" class="border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Q7 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-3 block">৭. আইসিটি কোর্সটি আরও কার্যকরভাবে পরিচালনার জন্য আপনার মতে কোন ধরনের অতিরিক্ত সহায়তা সবচেয়ে বেশি প্রয়োজন? <span class="text-sm font-normal text-slate-500">(একাধিক উত্তর নির্বাচন করা যাবে)</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-2">
                            @foreach([
                                "ইন-হাউজ শিক্ষক প্রশিক্ষণ।",
                                "রিফ্রেশার প্রশিক্ষণ।",
                                "ভিডিও টিউটোরিয়াল ও ডেমোনস্ট্রেশন।",
                                "উন্নত কম্পিউটার ল্যাব সুবিধা।",
                                "উন্নত ইন্টারনেট সংযোগ।",
                                "পরীক্ষার প্রস্তুতির জন্য অতিরিক্ত শিক্ষাসামগ্রী।",
                                "অনলাইন কোর্স ব্যবহারের নির্দেশিকা।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" wire:model="q7" value="{{ $opt }}" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Q8 -->
                    <div>
                        <label class="text-lg font-semibold text-slate-800 mb-3 block">৮. আইসিটি কোর্স সম্পর্কিত তথ্য ও আপডেট আপনি কোন কোন মাধ্যমে পেতে চান? <span class="text-sm font-normal text-slate-500">(একাধিক উত্তর নির্বাচন করা যাবে)</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-2 mb-3">
                            @foreach([
                                "জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল ওয়েবসাইট।",
                                "জাতীয় বিশ্ববিদ্যালয়ের অফিসিয়াল ফেসবুক পেজ।",
                                "কলেজ প্রশাসনের মাধ্যমে।",
                                "বিভাগ/শিক্ষকদের ফেসবুক বা হোয়াটসঅ্যাপ গ্রুপ।",
                                "ই-মেইল।",
                                "এসএমএস (SMS)",
                                "শিক্ষক প্রশিক্ষণ কর্মশালা।"
                            ] as $opt)
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" wire:model="q8" value="{{ $opt }}" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <label class="flex items-center mt-2 pl-2 w-full md:w-1/2">
                            <input type="checkbox" wire:model="q8" value="other" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-3 mr-3 text-slate-700 whitespace-nowrap">অন্যান্য (উল্লেখ করুন):</span>
                            <input type="text" wire:model="q8_other_text" class="flex-1 border-b border-slate-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-2 py-1 outline-none">
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-slate-100 flex justify-center">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-10 rounded-xl shadow-md transition duration-200 ease-in-out flex items-center disabled:opacity-70">
                            <span>জমা দিন</span>
                            <div wire:loading class="ml-2 w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </button>
                    </div>
                </form>

            @endif
        </div>
    </div>
</div>

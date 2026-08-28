<x-layouts::app.welcome
    title="বয়স ও চাকরির মেয়াদ ক্যালকুলেটর"
    description="নির্দিষ্ট তারিখে সঠিক বয়স, চাকরির মেয়াদ ও সম্ভাব্য অবসরের তারিখ হিসাব করুন।"
    keywords="age calculator, retirement calculator, চাকরির মেয়াদ, বয়স হিসাব"
>
    <section data-age-retirement-calculator class="mx-auto max-w-5xl" aria-labelledby="calculator-title">
        <div class="mb-8 text-center">
            <a href="{{ route('tools.index') }}" wire:navigate class="theme-primary-text inline-flex items-center gap-2 text-sm font-bold hover:underline">← সব টুলস</a>
            <h1 id="calculator-title" class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">বয়স ও চাকরির মেয়াদ ক্যালকুলেটর</h1>
            <p class="mx-auto mt-3 max-w-3xl text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-base">জন্মতারিখ ও কাট-অফ তারিখ দিয়ে বছর, মাস ও দিন হিসেবে বয়স জানুন। যোগদানের তারিখ দিলে একই সঙ্গে চাকরির মেয়াদও দেখা যাবে।</p>
        </div>

        <div class="grid overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20 lg:grid-cols-2">
            <div class="border-b border-slate-200 p-5 dark:border-slate-800 sm:p-7 lg:border-r lg:border-b-0">
                <form class="grid gap-5">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-200">জন্মতারিখ <span class="text-red-500">*</span>
                        <input name="birthDate" type="date" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950">
                    </label>

                    <label class="text-sm font-bold text-slate-700 dark:text-slate-200">যে তারিখে বয়স হিসাব করবেন <span class="text-red-500">*</span>
                        <input name="calculationDate" type="date" value="{{ now()->toDateString() }}" required class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950">
                        <span class="mt-1.5 block text-xs font-normal text-slate-500">আজকের তারিখ অথবা সার্কুলারের কাট-অফ তারিখ দিন।</span>
                    </label>

                    <label class="text-sm font-bold text-slate-700 dark:text-slate-200">চাকরিতে যোগদানের তারিখ <span class="font-normal text-slate-400">(ঐচ্ছিক)</span>
                        <input name="joiningDate" type="date" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950">
                    </label>

                    <label class="text-sm font-bold text-slate-700 dark:text-slate-200">অবসরের বয়স
                        <select name="retirementAge" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950">
                            <option value="59">৫৯ বছর</option>
                            <option value="60">৬০ বছর</option>
                            <option value="65">৬৫ বছর</option>
                        </select>
                    </label>

                    <button type="submit" class="theme-primary-bg theme-primary-hover rounded-xl px-5 py-3 font-bold text-white shadow-sm transition">হিসাব করুন</button>
                    <p data-calculation-status class="min-h-5 text-center text-sm font-medium text-slate-500 dark:text-slate-400" aria-live="polite">প্রয়োজনীয় তারিখ দিয়ে হিসাব করুন।</p>
                </form>
            </div>

            <div class="flex min-h-96 flex-col bg-slate-50 p-5 dark:bg-slate-950/60 sm:p-7">
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">হিসাবের ফলাফল</h2>

                <div data-calculation-result hidden class="mt-5 grid gap-4">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-950/30">
                        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">নির্দিষ্ট তারিখে সঠিক বয়স</p>
                        <p data-age-result class="mt-2 text-2xl font-extrabold text-emerald-900 dark:text-emerald-200"></p>
                    </div>

                    <div data-service-container hidden class="rounded-2xl border border-sky-200 bg-sky-50 p-5 dark:border-sky-900 dark:bg-sky-950/30">
                        <p class="text-sm font-bold text-sky-700 dark:text-sky-400">চাকরির মোট মেয়াদ</p>
                        <p data-service-result class="mt-2 text-xl font-extrabold text-sky-900 dark:text-sky-200"></p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">সম্ভাব্য অবসরের তারিখ</p>
                        <p data-retirement-date class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white"></p>
                        <div data-retirement-remaining-container class="mt-4 border-t border-slate-200 pt-4 dark:border-slate-700">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">অবসর পর্যন্ত বাকি</p>
                            <p data-retirement-remaining class="mt-1 font-bold text-slate-800 dark:text-slate-200"></p>
                        </div>
                    </div>

                    <p class="rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-800 dark:bg-amber-950/30 dark:text-amber-300">এটি তারিখভিত্তিক সহায়ক হিসাব। চূড়ান্ত PRL/অবসরের তারিখ নির্ধারণে প্রযোজ্য সরকারি বিধি ও কর্তৃপক্ষের সিদ্ধান্ত অনুসরণ করুন।</p>
                </div>

                <div data-result-placeholder class="my-auto py-10 text-center text-slate-400">
                    <svg class="mx-auto size-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2zm3 9h3v3H8v-3z"/></svg>
                    <p class="mt-4 text-sm">হিসাব করার পর ফলাফল এখানে দেখা যাবে</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts::app.welcome>

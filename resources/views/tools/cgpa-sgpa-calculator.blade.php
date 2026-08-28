<x-layouts::app.welcome
    title="জাতীয় বিশ্ববিদ্যালয় CGPA ও SGPA ক্যালকুলেটর"
    description="জাতীয় বিশ্ববিদ্যালয়ের গ্রেডিং স্কেল অনুযায়ী নম্বর বা গ্রেড দিয়ে SGPA ও CGPA হিসাব করুন।"
    keywords="NU CGPA calculator, SGPA calculator, জাতীয় বিশ্ববিদ্যালয় রেজাল্ট"
>
    <section data-cgpa-sgpa-calculator class="mx-auto max-w-6xl" aria-labelledby="result-calculator-title">
        <div class="mb-8 text-center">
            <a href="{{ route('tools.index') }}" wire:navigate class="theme-primary-text inline-flex items-center gap-2 text-sm font-bold hover:underline">← সব টুলস</a>
            <h1 id="result-calculator-title" class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">CGPA ও SGPA ক্যালকুলেটর</h1>
            <p class="mx-auto mt-3 max-w-3xl text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-base">কোর্সের নম্বর অথবা গ্রেড ও ক্রেডিট যোগ করুন। প্রতিটি পরিবর্তনের সঙ্গে SGPA এবং আগের পর্বসহ মোট CGPA তাৎক্ষণিকভাবে হিসাব হবে।</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <div class="grid gap-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">বর্তমান সেমিস্টার/বর্ষের কোর্স</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">নম্বর বা সরাসরি লেটার গ্রেড—যেকোনো একটি পদ্ধতি ব্যবহার করুন।</p>
                        </div>
                        <fieldset class="flex rounded-xl bg-slate-100 p-1 dark:bg-slate-800">
                            <legend class="sr-only">ইনপুটের ধরন</legend>
                            <label class="cursor-pointer"><input class="peer sr-only" type="radio" name="resultInputMode" value="marks" checked><span class="block rounded-lg px-3 py-2 text-xs font-bold text-slate-500 peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm dark:peer-checked:bg-slate-700 dark:peer-checked:text-emerald-300">নম্বর</span></label>
                            <label class="cursor-pointer"><input class="peer sr-only" type="radio" name="resultInputMode" value="grade"><span class="block rounded-lg px-3 py-2 text-xs font-bold text-slate-500 peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm dark:peer-checked:bg-slate-700 dark:peer-checked:text-emerald-300">গ্রেড</span></label>
                        </fieldset>
                    </div>

                    <div data-course-list class="mt-6 grid gap-3"></div>
                    <button type="button" data-add-course class="theme-primary-text mt-4 inline-flex items-center gap-2 rounded-xl border border-emerald-200 px-4 py-2.5 text-sm font-bold transition hover:bg-emerald-50 dark:border-emerald-900 dark:hover:bg-emerald-950/30"><span class="text-lg">+</span> আরেকটি কোর্স যোগ করুন</button>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">আগের সেমিস্টার/বর্ষসমূহ</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">মোট CGPA জানতে আগের প্রতিটি পর্বের SGPA ও মোট ক্রেডিট দিন।</p>
                        </div>
                        <button type="button" data-add-semester class="rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">+ পর্ব যোগ করুন</button>
                    </div>
                    <div data-semester-list class="mt-5 grid gap-3"></div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="overflow-hidden rounded-3xl bg-slate-950 text-white shadow-xl dark:border dark:border-slate-800">
                    <div class="bg-emerald-600 p-6 text-center">
                        <p class="text-sm font-bold text-emerald-100">বর্তমান ফলাফল (SGPA)</p>
                        <p data-sgpa-result class="mt-2 text-5xl font-black tracking-tight">0.00</p>
                        <p class="mt-2 text-xs text-emerald-100">মোট ক্রেডিট: <span data-current-credits>0.0</span></p>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-sm font-bold text-slate-300">সর্বমোট ফলাফল (CGPA)</p>
                        <p data-cgpa-result class="mt-2 text-4xl font-black tracking-tight">0.00</p>
                        <p class="mt-2 text-xs text-slate-400">সর্বমোট ক্রেডিট: <span data-total-credits>0.0</span></p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="font-extrabold text-slate-900 dark:text-white">গ্রেডিং স্কেল</h3>
                    <div class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-xs text-slate-600 dark:text-slate-300">
                        <span>৮০–১০০: A+ (4.00)</span><span>৭৫–৭৯: A (3.75)</span>
                        <span>৭০–৭৪: A− (3.50)</span><span>৬৫–৬৯: B+ (3.25)</span>
                        <span>৬০–৬৪: B (3.00)</span><span>৫৫–৫৯: B− (2.75)</span>
                        <span>৫০–৫৪: C+ (2.50)</span><span>৪৫–৪৯: C (2.25)</span>
                        <span>৪০–৪৪: D (2.00)</span><span>০–৩৯: F (0.00)</span>
                    </div>
                    <p class="mt-4 border-t border-slate-200 pt-4 text-xs leading-5 text-slate-500 dark:border-slate-700 dark:text-slate-400">এটি সহায়ক হিসাব। প্রকাশিত ফলাফল ও সংশ্লিষ্ট প্রোগ্রামের প্রবিধানই চূড়ান্ত হিসেবে গণ্য হবে।</p>
                </div>
            </aside>
        </div>
    </section>
</x-layouts::app.welcome>

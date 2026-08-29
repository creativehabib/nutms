<x-layouts::app.welcome
    title="ছবি ও স্বাক্ষর রিসাইজার"
    description="ছবি ও স্বাক্ষর নির্দিষ্ট পিক্সেল এবং ফাইল সাইজে রিসাইজ ও কম্প্রেস করুন।"
    keywords="image compressor, signature resizer, ছবি রিসাইজ"
>
    <section data-image-compressor data-local-only class="mx-auto max-w-5xl" aria-labelledby="compressor-title">
        <div class="mb-8 text-center">
            <a href="{{ route('tools.index') }}" wire:navigate class="theme-primary-text inline-flex items-center gap-2 text-sm font-bold hover:underline">← সব টুলস</a>
            <h1 id="compressor-title" class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">ছবি ও স্বাক্ষর রিসাইজার</h1>
            <p class="mx-auto mt-3 max-w-3xl text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-base">ছবি নির্বাচন করে প্রয়োজনীয় পিক্সেল ও সর্বোচ্চ ফাইল সাইজ দিন। পুরো প্রক্রিয়া আপনার ব্রাউজারেই হবে—ছবি কোনো সার্ভারে আপলোড হবে না।</p>
            <div class="mx-auto mt-4 flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5-3.5V12c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6.5L12 3l8 3.5z"/></svg>
                ১০০% ব্রাউজারে প্রসেস হবে · সার্ভারে সংরক্ষণ হবে না
            </div>
        </div>

        <div class="grid overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20 lg:grid-cols-[1.05fr_.95fr]">
            <div class="border-b border-slate-200 p-5 dark:border-slate-800 sm:p-7 lg:border-r lg:border-b-0">
                <form class="grid gap-5" data-no-upload>
                    <div>
                        <label for="image-input" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">ছবি বা স্বাক্ষর নির্বাচন করুন</label>
                        <label for="image-input" class="theme-primary-border flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-slate-50 px-5 py-8 text-center transition hover:bg-emerald-50/50 dark:bg-slate-950 dark:hover:bg-emerald-950/20">
                            <svg class="theme-primary-text size-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 16.5V18a2 2 0 002 2h14a2 2 0 002-2v-1.5M16 8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span class="mt-3 font-bold text-slate-800 dark:text-slate-100">ফাইল বেছে নিতে ক্লিক করুন</span>
                            <span class="mt-1 text-xs text-slate-500">JPG, PNG অথবা WebP</span>
                        </label>
                        <input id="image-input" data-image-input class="sr-only" type="file" accept="image/jpeg,image/png,image/webp" required>
                    </div>

                    <fieldset>
                        <legend class="mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">দ্রুত মাপ নির্বাচন</legend>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" data-size-preset data-width="300" data-height="300" data-max-size="100" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">প্রোফাইল ৩০০×৩০০</button>
                            <button type="button" data-size-preset data-width="300" data-height="80" data-max-size="60" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:text-slate-300">স্বাক্ষর ৩০০×৮০</button>
                        </div>
                    </fieldset>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-200">প্রস্থ (px)<input name="width" type="number" min="20" max="4000" value="300" required class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950"></label>
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-200">উচ্চতা (px)<input name="height" type="number" min="20" max="4000" value="300" required class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950"></label>
                        <label class="col-span-2 text-sm font-bold text-slate-700 dark:text-slate-200 sm:col-span-1">সর্বোচ্চ (KB)<input name="maxSize" type="number" min="10" max="5000" value="100" required class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950"></label>
                    </div>

                    <label class="text-sm font-bold text-slate-700 dark:text-slate-200">আউটপুট ফরম্যাট
                        <select name="format" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-normal outline-none focus:border-emerald-500 dark:border-slate-700 dark:bg-slate-950">
                            <option value="image/jpeg">JPG — সব জায়গায় গ্রহণযোগ্য</option>
                            <option value="image/png">PNG — স্বচ্ছ ব্যাকগ্রাউন্ডের জন্য</option>
                            <option value="image/webp">WebP — তুলনামূলক ছোট</option>
                        </select>
                    </label>

                    <fieldset class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                        <legend class="px-2 text-sm font-bold text-slate-700 dark:text-slate-200">ব্যাকগ্রাউন্ড</legend>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700"><input type="radio" name="backgroundMode" value="keep" checked class="accent-emerald-600"> অপরিবর্তিত</label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700"><input type="radio" name="backgroundMode" value="transparent" class="accent-emerald-600"> রিমুভ করুন</label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-700"><input type="radio" name="backgroundMode" value="solid" class="accent-emerald-600"> সলিড রঙ</label>
                        </div>
                        <div data-background-options hidden class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-200">রিমুভের মাত্রা: <span data-tolerance-value>৩০</span>
                                <input name="backgroundTolerance" type="range" min="5" max="100" value="30" class="mt-2 w-full accent-emerald-600">
                            </label>
                            <label data-solid-color hidden class="text-sm font-bold text-slate-700 dark:text-slate-200">নতুন ব্যাকগ্রাউন্ড
                                <span class="mt-2 flex items-center gap-3"><input name="backgroundColor" type="color" value="#ffffff" class="h-10 w-14 cursor-pointer rounded-lg border border-slate-200 bg-transparent p-1 dark:border-slate-700"><span class="font-normal text-slate-500">পছন্দের রঙ বেছে নিন</span></span>
                            </label>
                        </div>
                        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-amber-950 dark:border-amber-900/70 dark:bg-amber-950/30 dark:text-amber-100" role="note" aria-label="ব্যাকগ্রাউন্ড রিমুভের নির্দেশনা">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/></svg>
                                <div>
                                    <p class="text-sm font-extrabold">এক রঙের ব্যাকগ্রাউন্ডে ব্যবহার করুন</p>
                                    <p class="mt-1 text-xs leading-5 text-amber-800 dark:text-amber-200">এই টুল ছবির চার কোণের রঙ শনাক্ত করে। তাই সাদা বা অন্য কোনো সমান, এক রঙের ব্যাকগ্রাউন্ডই সঠিকভাবে রিমুভ হবে। ভালো ফলের জন্য ছবির চার কোণ পরিষ্কার রাখুন এবং প্রয়োজন অনুযায়ী রিমুভের মাত্রা সামঞ্জস্য করুন।</p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 text-xs leading-5 text-slate-600 dark:text-slate-300">
                            একাধিক রঙ বা জটিল ব্যাকগ্রাউন্ডের ছবি হলে
                            <a href="https://www.remove.bg/" target="_blank" rel="noopener noreferrer" class="font-bold text-emerald-700 underline decoration-emerald-300 underline-offset-2 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300">remove.bg ব্যবহার করুন<span class="sr-only"> (নতুন ট্যাবে খুলবে)</span></a>।
                            সেখানে ব্যাকগ্রাউন্ড রিমুভ করার পর PNG ফাইলটি এখানে এনে প্রয়োজনীয় মাপ ও সাইজে কম্প্রেস করতে পারবেন।
                        </p>
                    </fieldset>

                    <button type="submit" class="theme-primary-bg theme-primary-hover rounded-xl px-5 py-3 font-bold text-white shadow-sm transition disabled:cursor-wait disabled:opacity-60">রিসাইজ ও কম্প্রেস করুন</button>
                    <p data-status class="min-h-5 text-center text-sm font-medium text-slate-500 dark:text-slate-400" aria-live="polite">ছবি নির্বাচন করলে লাইভ প্রিভিউ দেখা যাবে।</p>
                </form>
            </div>

            <div class="flex min-h-96 flex-col bg-slate-50 p-5 dark:bg-slate-950/60 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">লাইভ প্রিভিউ</h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">ছবিটি ড্রাগ করে অবস্থান ঠিক করুন</p>
                    </div>
                    <button type="button" data-reset-position hidden class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:border-emerald-400 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">অবস্থান রিসেট</button>
                </div>
                <div data-preview-stage tabindex="0" role="application" aria-label="ছবির অবস্থান পরিবর্তনের জায়গা" class="mt-4 flex min-h-64 flex-1 touch-none select-none items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-[linear-gradient(45deg,#f1f5f9_25%,transparent_25%),linear-gradient(-45deg,#f1f5f9_25%,transparent_25%),linear-gradient(45deg,transparent_75%,#f1f5f9_75%),linear-gradient(-45deg,transparent_75%,#f1f5f9_75%)] bg-[size:20px_20px] outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900">
                    <div data-empty-state class="px-6 text-center text-sm text-slate-500">ছবি নির্বাচন করুন—তারপর প্রস্থ বা উচ্চতা পরিবর্তন করলে ফলাফল এখানে সঙ্গে সঙ্গে দেখা যাবে</div>
                    <img data-image-preview hidden draggable="false" class="max-h-96 max-w-full cursor-grab touch-none object-contain shadow-lg active:cursor-grabbing" alt="রিসাইজ ও কম্প্রেস করা ছবির লাইভ প্রিভিউ">
                </div>

                <div data-result hidden class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/30">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">ডাউনলোডের জন্য প্রস্তুত</p>
                            <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-400"><span data-result-dimensions></span> · <span data-result-size></span></p>
                        </div>
                        <a data-download class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">ডাউনলোড করুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts::app.welcome>

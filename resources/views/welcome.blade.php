<!DOCTYPE html>
<html lang="bn">
    <head>
        @include('partials.head', ['title' => 'জাতীয় বিশ্ববিদ্যালয় শিক্ষক প্রশিক্ষণ দপ্তর'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 -z-20 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.18),_transparent_35%),radial-gradient(circle_at_85%_25%,_rgba(59,130,246,0.2),_transparent_30%),linear-gradient(to_bottom,_#07111f,_#0f172a)]"></div>
            <div class="absolute inset-x-0 top-0 -z-10 h-px bg-gradient-to-r from-transparent via-emerald-300/70 to-transparent"></div>

            <header class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3" aria-label="হোম">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl border border-emerald-300/30 bg-emerald-400/10 text-emerald-300 shadow-lg shadow-emerald-950/30">
                        <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m3 9 9-5 9 5-9 5-9-5Zm3 3v5c3.5 2.7 8.5 2.7 12 0v-5m3-3v6"></path></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-bold text-white sm:text-base">জাতীয় বিশ্ববিদ্যালয়</span>
                        <span class="block truncate text-xs text-slate-400 sm:text-sm">শিক্ষক প্রশিক্ষণ দপ্তর</span>
                    </span>
                </a>

                <nav class="flex items-center gap-2" aria-label="প্রধান নেভিগেশন">
                    @auth
                        <flux:button :href="route('dashboard')" variant="primary" icon="squares-2x2">ড্যাশবোর্ড</flux:button>
                    @else
                        <flux:button :href="route('login')" icon="arrow-right-end-on-rectangle">লগইন</flux:button>
                    @endauth
                </nav>
            </header>

            <main>
                <section class="mx-auto grid min-h-[calc(100vh-5.5rem)] w-full max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1.08fr_0.92fr] lg:px-8 lg:py-20">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold text-emerald-200 sm:text-sm">
                            <span class="size-2 rounded-full bg-emerald-300 shadow-[0_0_12px_rgba(110,231,183,0.9)]"></span>
                            শিক্ষক তথ্য ব্যবস্থাপনার সমন্বিত প্ল্যাটফর্ম
                        </div>

                        <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                            শিক্ষক উন্নয়ন ও প্রশিক্ষণ ব্যবস্থাপনা এখন
                            <span class="bg-gradient-to-r from-emerald-300 to-cyan-300 bg-clip-text text-transparent">আরও সহজ ও কার্যকর</span>
                        </h1>

                        <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
                            জাতীয় বিশ্ববিদ্যালয়ের অধিভুক্ত কলেজের শিক্ষক তথ্য, আইসিটি প্রশিক্ষণ এবং কম্পিউটার ল্যাব-সংক্রান্ত উপাত্ত নিরাপদভাবে সংরক্ষণ, হালনাগাদ ও বিশ্লেষণের জন্য একটি আধুনিক ডিজিটাল ব্যবস্থা।
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <flux:button :href="route('teachers.manage')" variant="primary" icon-trailing="arrow-right">শিক্ষক ব্যবস্থাপনা খুলুন</flux:button>
                            @else
                                <flux:button :href="route('login')" variant="primary" icon-trailing="arrow-right">ব্যবস্থাপনা সিস্টেমে প্রবেশ করুন</flux:button>
                            @endauth
                            <flux:button href="#features" variant="ghost">সিস্টেমের সুবিধা দেখুন</flux:button>
                        </div>

                        <div class="mt-10 grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                                <p class="text-2xl font-black text-emerald-300">Excel</p>
                                <p class="mt-1 text-xs leading-5 text-slate-400">দ্রুত শিক্ষক ডেটা ইম্পোর্ট</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                                <p class="text-2xl font-black text-cyan-300">Live</p>
                                <p class="mt-1 text-xs leading-5 text-slate-400">সার্চ, ফিল্টার ও রিপোর্ট</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                                <p class="text-2xl font-black text-blue-300">Safe</p>
                                <p class="mt-1 text-xs leading-5 text-slate-400">ট্র্যাশ ও ডেটা পুনরুদ্ধার</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-xl">
                        <div class="absolute -inset-8 -z-10 rounded-full bg-emerald-400/10 blur-3xl"></div>
                        <div class="overflow-hidden rounded-3xl border border-white/15 bg-white/7 p-3 shadow-2xl shadow-black/40 backdrop-blur-xl sm:p-4">
                            <div class="rounded-2xl border border-white/10 bg-slate-900/85 p-5 sm:p-7">
                                <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-5">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">Training Office</p>
                                        <h2 class="mt-1 text-xl font-bold text-white">তথ্য ব্যবস্থাপনা কেন্দ্র</h2>
                                    </div>
                                    <span class="flex size-11 items-center justify-center rounded-2xl bg-blue-400/10 text-blue-300">
                                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87m-2-12a4 4 0 0 1 0 7.75"></path></svg>
                                    </span>
                                </div>

                                <div class="mt-6 space-y-3">
                                    <div class="flex items-center gap-4 rounded-2xl border border-white/8 bg-white/5 p-4">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300">
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7 9 18l-5-5"></path></svg>
                                        </span>
                                        <div>
                                            <p class="font-semibold text-white">কেন্দ্রীয় শিক্ষক ডেটাবেস</p>
                                            <p class="mt-1 text-xs text-slate-400">কলেজভিত্তিক তথ্য সংরক্ষণ ও হালনাগাদ</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 rounded-2xl border border-white/8 bg-white/5 p-4">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V9m5 10V5m5 14v-7m5 7V3"></path></svg>
                                        </span>
                                        <div>
                                            <p class="font-semibold text-white">প্রশিক্ষণ অগ্রগতি পর্যবেক্ষণ</p>
                                            <p class="mt-1 text-xs text-slate-400">আইসিটি ও অন্যান্য প্রশিক্ষণের সারসংক্ষেপ</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 rounded-2xl border border-white/8 bg-white/5 p-4">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-400/10 text-blue-300">
                                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 10h10M9 14h6M11 18h2"></path></svg>
                                        </span>
                                        <div>
                                            <p class="font-semibold text-white">ল্যাব সুবিধা বিশ্লেষণ</p>
                                            <p class="mt-1 text-xs text-slate-400">কলেজের কম্পিউটার ও ল্যাব তথ্যের চিত্র</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="border-y border-white/10 bg-white/3">
                    <div class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                        <div class="max-w-2xl">
                            <p class="text-sm font-bold text-emerald-300">দপ্তরের কার্যক্রমে ডিজিটাল সহায়তা</p>
                            <h2 class="mt-3 text-3xl font-black text-white sm:text-4xl">এক প্ল্যাটফর্মে প্রয়োজনীয় সব সুবিধা</h2>
                            <p class="mt-4 leading-7 text-slate-400">সঠিক তথ্যের ভিত্তিতে শিক্ষক প্রশিক্ষণ পরিকল্পনা ও প্রাতিষ্ঠানিক সিদ্ধান্ত গ্রহণকে আরও কার্যকর করুন।</p>
                        </div>

                        <div class="mt-10 grid gap-4 md:grid-cols-3">
                            <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0-12 4 4m-4-4L8 7M5 21h14a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2"></path></svg>
                                </span>
                                <h3 class="mt-5 text-lg font-bold text-white">সহজ ডেটা ইম্পোর্ট</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">Excel ও CSV ফাইল থেকে কলেজভিত্তিক শিক্ষক তথ্য দ্রুত ও নির্ভুলভাবে যুক্ত করুন।</p>
                            </article>
                            <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 7.5 7.5M21 3l-7.5 7.5M3 21l7.5-7.5M21 21l-7.5-7.5"></path></svg>
                                </span>
                                <h3 class="mt-5 text-lg font-bold text-white">দ্রুত অনুসন্ধান ও ফিল্টার</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">নাম, TMIS ID, বিষয়, কলেজ এবং ল্যাব সুবিধা অনুযায়ী প্রয়োজনীয় তথ্য খুঁজুন।</p>
                            </article>
                            <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-blue-400/10 text-blue-300">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"></path></svg>
                                </span>
                                <h3 class="mt-5 text-lg font-bold text-white">নিরাপদ তথ্য ব্যবস্থাপনা</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">সম্পাদনা, bulk action, soft delete ও restore সুবিধায় তথ্য পরিচালনা করুন আত্মবিশ্বাসের সঙ্গে।</p>
                            </article>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>© {{ now()->year }} জাতীয় বিশ্ববিদ্যালয় শিক্ষক প্রশিক্ষণ দপ্তর</p>
                <p>শিক্ষক উন্নয়ন, তথ্য ব্যবস্থাপনা ও প্রশিক্ষণ পরিকল্পনা</p>
            </footer>
        </div>
        @fluxScripts
    </body>
</html>

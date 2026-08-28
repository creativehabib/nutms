<x-layouts::app.welcome
    title="অনলাইন টুলস"
    description="শিক্ষক ও শিক্ষা প্রতিষ্ঠানের দৈনন্দিন কাজের জন্য প্রয়োজনীয় অনলাইন টুলস।"
    keywords="শিক্ষক টুলস, ছবি রিসাইজ, স্বাক্ষর কম্প্রেস"
>
    <section class="mx-auto max-w-6xl" aria-labelledby="tools-title">
        <div class="mx-auto max-w-3xl text-center">
            <span class="theme-primary-soft theme-primary-text inline-flex rounded-full px-4 py-1.5 text-sm font-bold">সহজ ও নিরাপদ</span>
            <h1 id="tools-title" class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">প্রয়োজনীয় অনলাইন টুলস</h1>
            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-base">ছোট ছোট দৈনন্দিন কাজ দ্রুত সম্পন্ন করুন। নতুন টুল পর্যায়ক্রমে এখানে যুক্ত হবে।</p>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('tools.image-signature-compressor') }}" wire:navigate class="group flex min-h-72 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700">
                <div class="theme-primary-soft theme-primary-text flex size-14 items-center justify-center rounded-2xl">
                    <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2 1.6-1.6a2 2 0 012.8 0L20 14m-14 6h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zm9-12h.01"/></svg>
                </div>
                <h2 class="mt-6 text-xl font-extrabold text-slate-900 dark:text-white">ছবি ও স্বাক্ষর রিসাইজার</h2>
                <p class="mt-3 flex-1 text-sm leading-7 text-slate-600 dark:text-slate-400">ছবি বা স্ক্যান করা স্বাক্ষর নির্দিষ্ট পিক্সেল ও ফাইল সাইজে ক্রপ এবং কম্প্রেস করুন।</p>
                <span class="theme-primary-text mt-6 inline-flex items-center gap-2 text-sm font-bold">টুলটি ব্যবহার করুন <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>

            <a href="{{ route('tools.age-retirement-calculator') }}" wire:navigate class="group flex min-h-72 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700">
                <div class="theme-primary-soft theme-primary-text flex size-14 items-center justify-center rounded-2xl">
                    <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 2v3m8-3v3M3.5 9h17M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2zm3 9h3v3H8v-3z"/></svg>
                </div>
                <h2 class="mt-6 text-xl font-extrabold text-slate-900 dark:text-white">বয়স ও চাকরির মেয়াদ ক্যালকুলেটর</h2>
                <p class="mt-3 flex-1 text-sm leading-7 text-slate-600 dark:text-slate-400">নির্দিষ্ট তারিখে সঠিক বয়স ও চাকরির মেয়াদ এবং সম্ভাব্য অবসরের তারিখ হিসাব করুন।</p>
                <span class="theme-primary-text mt-6 inline-flex items-center gap-2 text-sm font-bold">হিসাব করুন <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span></span>
            </a>

            <div class="flex min-h-72 flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white/50 p-6 text-center dark:border-slate-700 dark:bg-slate-900/40 sm:col-span-2 lg:col-span-1">
                <div class="flex size-12 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400 dark:bg-slate-800">+</div>
                <h2 class="mt-4 font-bold text-slate-700 dark:text-slate-300">আরও টুল আসছে</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">নতুন প্রয়োজনীয় টুল পর্যায়ক্রমে যুক্ত করা হবে।</p>
            </div>
        </div>
    </section>
</x-layouts::app.welcome>

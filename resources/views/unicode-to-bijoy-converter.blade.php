@push('styles')
    <link rel="stylesheet" href="https://fonts.maateen.me/sutonny-mj/font.css">
    <style>
        .bijoy-text {
            font-family: 'SutonnyMJ', sans-serif;
        }
    </style>
@endpush

<x-layouts::app.welcome
    title="ইউনিকোড টু বিজয় কনভার্টার"
    description="বাংলা ইউনিকোড ও বিজয় কি-বোর্ডের লেখার মধ্যে সহজে রূপান্তর করুন।"
    keywords="Unicode to Bijoy, Bijoy to Unicode, বাংলা কনভার্টার"
>
    <section
        data-unicode-bijoy-converter
        class="mx-auto max-w-5xl"
        aria-labelledby="converter-title"
    >
        <div class="mb-8 text-center">
            <span class="theme-primary-soft theme-primary-text inline-flex rounded-full px-4 py-1.5 text-sm font-bold">
                বাংলা লেখার সহজ টুল
            </span>
            <h1 id="converter-title" class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                ইউনিকোড টু বিজয় কনভার্টার
            </h1>
            <p class="mx-auto mt-3 max-w-3xl text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-base">
                ইউনিকোড থেকে বিজয় অথবা বিজয় থেকে ইউনিকোডে বাংলা লেখা রূপান্তর করুন। চাইলে ভয়েস টাইপিং দিয়েও লেখা যোগ করতে পারবেন।
            </p>

            <button
                type="button"
                data-voice-typing
                class="theme-primary-border theme-primary-text mt-5 inline-flex items-center gap-2 rounded-full border bg-white px-5 py-2.5 text-sm font-bold shadow-sm transition hover:-translate-y-0.5 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 dark:bg-slate-900"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18.5a6.5 6.5 0 0 0 6.5-6.5M12 18.5A6.5 6.5 0 0 1 5.5 12M12 18.5V22m-3 0h6M12 15a3 3 0 0 0 3-3V5a3 3 0 1 0-6 0v7a3 3 0 0 0 3 3Z"/>
                </svg>
                <span data-voice-label>ভয়েস টাইপিং শুরু করুন</span>
            </button>
            <p data-converter-status class="mt-3 min-h-5 text-sm font-medium text-slate-500 dark:text-slate-400" aria-live="polite"></p>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
            <div class="p-4 sm:p-6">
                <label for="unicode-bijoy-input" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">ইউনিকোড লেখা</label>
                <textarea
                    id="unicode-bijoy-input"
                    data-converter-input
                    rows="8"
                    spellcheck="false"
                    placeholder="ইউনিকোডে লেখা এখানে লিখুন অথবা পেস্ট করুন..."
                    class="theme-primary-border min-h-48 w-full resize-y rounded-2xl border bg-slate-50 p-4 text-lg leading-8 text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4 focus:ring-emerald-500/10 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                ></textarea>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2 border-y border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-950/60 sm:gap-3">
                <button type="button" data-convert="unicode-to-bijoy" class="theme-primary-bg theme-primary-hover inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm">
                    ইউনিকোড থেকে বিজয় <span aria-hidden="true">↓</span>
                </button>
                <button type="button" data-convert="bijoy-to-unicode" class="theme-primary-bg theme-primary-hover inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm">
                    বিজয় থেকে ইউনিকোড <span aria-hidden="true">↑</span>
                </button>
                <button type="button" data-convert="fix-unicode" class="rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500">ইউনিকোড ঠিক করুন</button>
                <button type="button" data-convert="fix-bijoy" class="rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500">বিজয় ঠিক করুন</button>
                <button type="button" data-clear-converter class="rounded-xl bg-red-500 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-600">মুছে ফেলুন</button>
            </div>

            <div class="p-4 sm:p-6">
                <div class="mb-2 flex items-center justify-between gap-4">
                    <label for="unicode-bijoy-output" class="block text-sm font-bold text-slate-700 dark:text-slate-200">বিজয় কি-বোর্ডের লেখা</label>
                    <button type="button" data-copy-converter class="theme-primary-text inline-flex items-center gap-1.5 text-sm font-bold hover:underline">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2M5 8h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2Z"/></svg>
                        কপি করুন
                    </button>
                </div>
                <textarea
                    id="unicode-bijoy-output"
                    data-converter-output
                    rows="8"
                    spellcheck="false"
                    placeholder="বিজয় কি-বোর্ডের লেখা এখানে লিখুন অথবা পেস্ট করুন..."
                    class="bijoy-text min-h-48 w-full resize-y rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xl leading-9 text-slate-900 outline-none placeholder:font-sans placeholder:text-lg placeholder:text-slate-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500"
                ></textarea>
            </div>
        </div>

        <livewire:text-converter />
    </section>
</x-layouts::app.welcome>

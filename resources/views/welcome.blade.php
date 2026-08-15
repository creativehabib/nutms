<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা | জাতীয় বিশ্ববিদ্যালয়</title>

    <meta name="description"
          content="জাতীয় বিশ্ববিদ্যালয়ের শিক্ষক ব্যবস্থাপনা ও প্রশিক্ষণ পরিচালনা সংক্রান্ত সমন্বিত ডিজিটাল প্ল্যাটফর্ম।">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>
        * {
            font-family: 'Noto Sans Bengali', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        .hero-pattern {
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,.10) 0, transparent 30%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,.08) 0, transparent 30%);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

{{-- =========================
    NAVBAR
========================== --}}
<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-3">

            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-700 text-white shadow">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-7 w-7"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M5 12v5c4 3 10 3 14 0v-5"/>
                </svg>
            </div>

            <div>
                <h1 class="text-lg font-bold leading-tight text-emerald-800">
                    জাতীয় বিশ্ববিদ্যালয়
                </h1>

                <p class="text-xs text-slate-500">
                    শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা
                </p>
            </div>

        </a>


        {{-- Desktop Menu --}}
        <nav class="hidden items-center gap-7 md:flex">

            <a href="#home"
               class="text-sm font-medium text-slate-700 transition hover:text-emerald-700">
                হোম
            </a>

            <a href="#about"
               class="text-sm font-medium text-slate-700 transition hover:text-emerald-700">
                আমাদের সম্পর্কে
            </a>

            <a href="#services"
               class="text-sm font-medium text-slate-700 transition hover:text-emerald-700">
                সেবাসমূহ
            </a>

            <a href="#training"
               class="text-sm font-medium text-slate-700 transition hover:text-emerald-700">
                প্রশিক্ষণ
            </a>

            <a href="#notices"
               class="text-sm font-medium text-slate-700 transition hover:text-emerald-700">
                নোটিশ
            </a>

            <a href="#contact"
               class="text-sm font-medium text-slate-700 transition hover:text-emerald-700">
                যোগাযোগ
            </a>

        </nav>


        {{-- Login --}}
        <div class="hidden md:block">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m-5-4l5-5m0 0l-5-5m5 5H3"/>
                </svg>

                লগইন
            </a>
        </div>

    </div>
</header>


{{-- =========================
    HERO
========================== --}}
<section
    id="home"
    class="relative overflow-hidden bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900"
>

    <div class="absolute inset-0 opacity-10">
        <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-white"></div>
        <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-white"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">

        <div class="grid items-center gap-12 lg:grid-cols-2">

            {{-- Left --}}
            <div>

                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-300/30 bg-white/10 px-4 py-2 text-sm text-emerald-50 backdrop-blur">

                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>

                    ডিজিটাল শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা প্ল্যাটফর্ম

                </div>

                <h2 class="text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">

                    শিক্ষক ব্যবস্থাপনা
                    <span class="text-emerald-300">ও প্রশিক্ষণের</span>
                    আধুনিক সমাধান

                </h2>

                <p class="mt-6 max-w-xl text-lg leading-8 text-emerald-50/80">

                    জাতীয় বিশ্ববিদ্যালয়ের অধিভুক্ত কলেজসমূহের শিক্ষক ব্যবস্থাপনা,
                    প্রশিক্ষণ কার্যক্রম, অংশগ্রহণ, মূল্যায়ন এবং প্রশাসনিক কার্যক্রম
                    একটি সমন্বিত ডিজিটাল প্ল্যাটফর্মের মাধ্যমে পরিচালনা করুন।

                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 font-bold text-emerald-800 shadow-lg transition hover:bg-emerald-50">

                        সিস্টেমে প্রবেশ করুন

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>

                    </a>

                    <a href="#about"
                       class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-6 py-3.5 font-semibold text-white backdrop-blur transition hover:bg-white/20">

                        বিস্তারিত জানুন

                    </a>

                </div>

            </div>


            {{-- Right Dashboard Preview --}}
            <div class="relative">

                <div class="rounded-2xl border border-white/20 bg-white/10 p-3 shadow-2xl backdrop-blur">

                    <div class="overflow-hidden rounded-xl bg-white">

                        {{-- Fake dashboard header --}}
                        <div class="flex items-center justify-between border-b px-5 py-4">

                            <div>
                                <div class="h-3 w-28 rounded bg-slate-200"></div>
                                <div class="mt-2 h-2 w-20 rounded bg-slate-100"></div>
                            </div>

                            <div class="h-9 w-9 rounded-full bg-emerald-100"></div>

                        </div>

                        <div class="p-5">

                            <div class="grid grid-cols-3 gap-3">

                                <div class="rounded-xl bg-emerald-50 p-4">
                                    <div class="text-xs text-slate-500">
                                        মোট শিক্ষক
                                    </div>
                                    <div class="mt-2 text-2xl font-bold text-emerald-700">
                                        ১২,৫৪০
                                    </div>
                                </div>

                                <div class="rounded-xl bg-blue-50 p-4">
                                    <div class="text-xs text-slate-500">
                                        প্রশিক্ষণ
                                    </div>
                                    <div class="mt-2 text-2xl font-bold text-blue-700">
                                        ৮৬
                                    </div>
                                </div>

                                <div class="rounded-xl bg-amber-50 p-4">
                                    <div class="text-xs text-slate-500">
                                        অংশগ্রহণ
                                    </div>
                                    <div class="mt-2 text-2xl font-bold text-amber-700">
                                        ৯৪%
                                    </div>
                                </div>

                            </div>

                            <div class="mt-5 rounded-xl border p-4">

                                <div class="flex items-center justify-between">
                                        <span class="font-semibold">
                                            প্রশিক্ষণ অগ্রগতি
                                        </span>

                                    <span class="text-xs text-emerald-700">
                                            এই বছর
                                        </span>
                                </div>

                                <div class="mt-5 space-y-4">

                                    <div>
                                        <div class="mb-2 flex justify-between text-xs">
                                            <span>শিক্ষক উন্নয়ন</span>
                                            <span>৮২%</span>
                                        </div>

                                        <div class="h-2 rounded-full bg-slate-100">
                                            <div class="h-2 w-[82%] rounded-full bg-emerald-600"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="mb-2 flex justify-between text-xs">
                                            <span>প্রশিক্ষণ সম্পন্ন</span>
                                            <span>৭৪%</span>
                                        </div>

                                        <div class="h-2 rounded-full bg-slate-100">
                                            <div class="h-2 w-[74%] rounded-full bg-blue-600"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="mb-2 flex justify-between text-xs">
                                            <span>মূল্যায়ন</span>
                                            <span>৬৮%</span>
                                        </div>

                                        <div class="h-2 rounded-full bg-slate-100">
                                            <div class="h-2 w-[68%] rounded-full bg-amber-500"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================
    STATS
========================== --}}
<section class="-mt-8 relative z-10">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid overflow-hidden rounded-2xl bg-white shadow-xl sm:grid-cols-2 lg:grid-cols-4">

            <div class="border-b p-6 text-center sm:border-r lg:border-b-0">
                <div class="text-3xl font-extrabold text-emerald-700">
                    ১২,৫৪০+
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    নিবন্ধিত শিক্ষক
                </div>
            </div>

            <div class="border-b p-6 text-center lg:border-b-0 lg:border-r">
                <div class="text-3xl font-extrabold text-emerald-700">
                    ৮৬+
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    প্রশিক্ষণ কার্যক্রম
                </div>
            </div>

            <div class="border-b p-6 text-center sm:border-r lg:border-b-0">
                <div class="text-3xl font-extrabold text-emerald-700">
                    ১,২৫০+
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    প্রশিক্ষক
                </div>
            </div>

            <div class="p-6 text-center">
                <div class="text-3xl font-extrabold text-emerald-700">
                    ৯৮%
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    সিস্টেম আপটাইম
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================
    ABOUT
========================== --}}
<section id="about" class="py-20 lg:py-28">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid items-center gap-12 lg:grid-cols-2">

            <div>

                    <span class="text-sm font-bold uppercase tracking-wider text-emerald-700">
                        আমাদের প্ল্যাটফর্ম
                    </span>

                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    একটি প্ল্যাটফর্মে সম্পূর্ণ ব্যবস্থাপনা
                </h2>

                <p class="mt-5 leading-8 text-slate-600">

                    শিক্ষক সংক্রান্ত তথ্য সংরক্ষণ থেকে শুরু করে প্রশিক্ষণ পরিকল্পনা,
                    প্রশিক্ষণার্থী নির্বাচন, উপস্থিতি, মূল্যায়ন এবং রিপোর্টিং—
                    সকল কার্যক্রম একটি কেন্দ্রীয় সিস্টেমের মাধ্যমে পরিচালনা করা যাবে।

                </p>

                <div class="mt-7 space-y-4">

                    @foreach([
                        'কেন্দ্রীয় শিক্ষক তথ্য ব্যবস্থাপনা',
                        'অনলাইন প্রশিক্ষণ নিবন্ধন ও ব্যবস্থাপনা',
                        'উপস্থিতি ও মূল্যায়ন ব্যবস্থাপনা',
                        'স্বয়ংক্রিয় রিপোর্ট ও ড্যাশবোর্ড'
                    ] as $item)

                        <div class="flex items-center gap-3">

                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                ✓
                            </div>

                            <span class="text-slate-700">
                                    {{ $item }}
                                </span>

                        </div>

                    @endforeach

                </div>

            </div>


            <div class="grid grid-cols-2 gap-4">

                <div class="rounded-2xl bg-emerald-700 p-7 text-white shadow-lg">
                    <div class="text-4xl">👨‍🏫</div>
                    <h3 class="mt-5 text-xl font-bold">
                        শিক্ষক ব্যবস্থাপনা
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-emerald-50/80">
                        শিক্ষক প্রোফাইল, কর্মস্থল, বিষয়, অভিজ্ঞতা ও অন্যান্য তথ্যের সমন্বিত ব্যবস্থাপনা।
                    </p>
                </div>

                <div class="mt-8 rounded-2xl bg-white p-7 shadow-lg ring-1 ring-slate-100">
                    <div class="text-4xl">🎓</div>
                    <h3 class="mt-5 text-xl font-bold text-slate-900">
                        প্রশিক্ষণ
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        প্রশিক্ষণ আয়োজন, নিবন্ধন, অংশগ্রহণ ও সম্পন্ন করার তথ্য।
                    </p>
                </div>

                <div class="rounded-2xl bg-white p-7 shadow-lg ring-1 ring-slate-100">
                    <div class="text-4xl">📊</div>
                    <h3 class="mt-5 text-xl font-bold text-slate-900">
                        রিপোর্টিং
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        প্রয়োজনীয় রিপোর্ট ও পরিসংখ্যান দ্রুত তৈরি করুন।
                    </p>
                </div>

                <div class="mt-8 rounded-2xl bg-slate-900 p-7 text-white shadow-lg">
                    <div class="text-4xl">🔐</div>
                    <h3 class="mt-5 text-xl font-bold">
                        নিরাপদ ব্যবস্থাপনা
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        Role-based access এবং নিরাপদ ডেটা ব্যবস্থাপনার সুবিধা।
                    </p>
                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================
    SERVICES
========================== --}}
<section id="services" class="bg-white py-20 lg:py-28">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-2xl text-center">

                <span class="text-sm font-bold uppercase tracking-wider text-emerald-700">
                    প্রধান সেবাসমূহ
                </span>

            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                আপনার প্রয়োজনীয় সকল কার্যক্রম
            </h2>

            <p class="mt-4 leading-7 text-slate-500">
                শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনার জন্য প্রয়োজনীয় গুরুত্বপূর্ণ
                কার্যক্রম একটি প্ল্যাটফর্মে।
            </p>

        </div>


        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">

            @php
                $services = [
                    [
                        'icon' => '👨‍🏫',
                        'title' => 'শিক্ষক ব্যবস্থাপনা',
                        'description' => 'শিক্ষকের ব্যক্তিগত, একাডেমিক ও কর্মসংক্রান্ত তথ্য সংরক্ষণ ও ব্যবস্থাপনা।'
                    ],
                    [
                        'icon' => '🎯',
                        'title' => 'প্রশিক্ষণ পরিকল্পনা',
                        'description' => 'প্রশিক্ষণ তৈরি, সময়সূচি, আসন সংখ্যা, প্রশিক্ষক ও স্থান ব্যবস্থাপনা।'
                    ],
                    [
                        'icon' => '📝',
                        'title' => 'অনলাইন নিবন্ধন',
                        'description' => 'শিক্ষকরা অনলাইনে প্রশিক্ষণের জন্য আবেদন ও নিবন্ধন করতে পারবেন।'
                    ],
                    [
                        'icon' => '📅',
                        'title' => 'প্রশিক্ষণ ক্যালেন্ডার',
                        'description' => 'সকল চলমান ও আসন্ন প্রশিক্ষণ কার্যক্রম এক নজরে দেখুন।'
                    ],
                    [
                        'icon' => '📋',
                        'title' => 'উপস্থিতি ব্যবস্থাপনা',
                        'description' => 'প্রশিক্ষণে অংশগ্রহণকারীদের উপস্থিতি ডিজিটালভাবে সংরক্ষণ।'
                    ],
                    [
                        'icon' => '📈',
                        'title' => 'রিপোর্ট ও বিশ্লেষণ',
                        'description' => 'প্রশিক্ষণ ও শিক্ষক সংক্রান্ত বিভিন্ন রিপোর্ট সহজে তৈরি করুন।'
                    ],
                ];
            @endphp


            @foreach($services as $service)

                <div class="group rounded-2xl border border-slate-100 bg-slate-50 p-7 transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-xl">

                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-100 text-3xl transition group-hover:bg-emerald-700">
                        {{ $service['icon'] }}
                    </div>

                    <h3 class="mt-6 text-xl font-bold text-slate-900">
                        {{ $service['title'] }}
                    </h3>

                    <p class="mt-3 leading-7 text-slate-500">
                        {{ $service['description'] }}
                    </p>

                    <a href="{{ route('login') }}"
                       class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-emerald-700">

                        বিস্তারিত

                        <span>→</span>

                    </a>

                </div>

            @endforeach

        </div>

    </div>

</section>


{{-- =========================
    TRAINING
========================== --}}
<section id="training" class="bg-slate-900 py-20 lg:py-28">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="grid items-center gap-12 lg:grid-cols-2">

            <div>

                    <span class="text-sm font-bold uppercase tracking-wider text-emerald-400">
                        প্রশিক্ষণ ব্যবস্থাপনা
                    </span>

                <h2 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">
                    প্রশিক্ষণ কার্যক্রমকে করুন আরও সহজ ও কার্যকর
                </h2>

                <p class="mt-5 leading-8 text-slate-300">
                    প্রশিক্ষণ আয়োজন থেকে শুরু করে অংশগ্রহণকারী নির্বাচন,
                    উপস্থিতি, মূল্যায়ন এবং সার্টিফিকেট—সম্পূর্ণ workflow
                    একটি সিস্টেমের মাধ্যমে পরিচালনা করা সম্ভব।
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-2">

                    <div class="rounded-xl bg-white/5 p-5 ring-1 ring-white/10">
                        <div class="text-2xl">01</div>
                        <div class="mt-2 font-semibold text-white">
                            প্রশিক্ষণ তৈরি
                        </div>
                    </div>

                    <div class="rounded-xl bg-white/5 p-5 ring-1 ring-white/10">
                        <div class="text-2xl">02</div>
                        <div class="mt-2 font-semibold text-white">
                            অংশগ্রহণকারী নির্বাচন
                        </div>
                    </div>

                    <div class="rounded-xl bg-white/5 p-5 ring-1 ring-white/10">
                        <div class="text-2xl">03</div>
                        <div class="mt-2 font-semibold text-white">
                            উপস্থিতি ও মূল্যায়ন
                        </div>
                    </div>

                    <div class="rounded-xl bg-white/5 p-5 ring-1 ring-white/10">
                        <div class="text-2xl">04</div>
                        <div class="mt-2 font-semibold text-white">
                            রিপোর্ট ও সার্টিফিকেট
                        </div>
                    </div>

                </div>

            </div>


            <div class="rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 p-1 shadow-2xl">

                <div class="rounded-[22px] bg-slate-900 p-7">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-slate-400">
                                আসন্ন প্রশিক্ষণ
                            </p>

                            <h3 class="mt-1 text-xl font-bold text-white">
                                শিক্ষক উন্নয়ন প্রশিক্ষণ
                            </h3>
                        </div>

                        <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400">
                                চলমান
                            </span>

                    </div>

                    <div class="mt-7 space-y-4">

                        <div class="rounded-xl bg-white/5 p-4">
                            <div class="flex justify-between">
                                    <span class="text-sm text-slate-300">
                                        তারিখ
                                    </span>

                                <span class="text-sm font-semibold text-white">
                                        ১৫ সেপ্টেম্বর ২০২৬
                                    </span>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white/5 p-4">
                            <div class="flex justify-between">
                                    <span class="text-sm text-slate-300">
                                        আসন সংখ্যা
                                    </span>

                                <span class="text-sm font-semibold text-white">
                                        ৫০ জন
                                    </span>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white/5 p-4">
                            <div class="flex justify-between">
                                    <span class="text-sm text-slate-300">
                                        নিবন্ধন
                                    </span>

                                <span class="text-sm font-semibold text-emerald-400">
                                        ৩৮ / ৫০
                                    </span>
                            </div>

                            <div class="mt-3 h-2 rounded-full bg-white/10">
                                <div class="h-2 w-[76%] rounded-full bg-emerald-500"></div>
                            </div>
                        </div>

                    </div>

                    <a href="{{ route('login') }}"
                       class="mt-6 flex items-center justify-center rounded-xl bg-emerald-600 py-3 font-semibold text-white transition hover:bg-emerald-500">
                        প্রশিক্ষণ দেখুন
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================
    NOTICE
========================== --}}
<section id="notices" class="py-20 lg:py-28">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

            <div>

                    <span class="text-sm font-bold uppercase tracking-wider text-emerald-700">
                        সর্বশেষ আপডেট
                    </span>

                <h2 class="mt-3 text-3xl font-extrabold text-slate-900">
                    নোটিশ ও ঘোষণা
                </h2>

            </div>

            <a href="{{ route('login') }}"
               class="font-semibold text-emerald-700">
                সকল নোটিশ →
            </a>

        </div>


        <div class="mt-10 divide-y overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">

            @php
                $notices = [
                    [
                        'date' => '১২ আগস্ট ২০২৬',
                        'title' => 'শিক্ষক প্রশিক্ষণ কার্যক্রমে অংশগ্রহণের জন্য নিবন্ধন বিজ্ঞপ্তি',
                        'type' => 'প্রশিক্ষণ'
                    ],
                    [
                        'date' => '০৮ আগস্ট ২০২৬',
                        'title' => 'নতুন প্রশিক্ষণ ব্যাচের সময়সূচি প্রকাশ',
                        'type' => 'নোটিশ'
                    ],
                    [
                        'date' => '০২ আগস্ট ২০২৬',
                        'title' => 'শিক্ষক তথ্য হালনাগাদ সংক্রান্ত বিজ্ঞপ্তি',
                        'type' => 'গুরুত্বপূর্ণ'
                    ],
                ];
            @endphp

            @foreach($notices as $notice)

                <a href="{{ route('login') }}"
                   class="flex flex-col gap-4 p-6 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-start gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            📢
                        </div>

                        <div>

                            <h3 class="font-bold text-slate-800">
                                {{ $notice['title'] }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                প্রকাশিত: {{ $notice['date'] }}
                            </p>

                        </div>

                    </div>

                    <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            {{ $notice['type'] }}
                        </span>

                </a>

            @endforeach

        </div>

    </div>

</section>


{{-- =========================
    CTA
========================== --}}
<section class="px-4 pb-20 sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-700 to-teal-700">

        <div class="px-6 py-14 text-center sm:px-12 lg:py-16">

            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                ডিজিটাল ব্যবস্থাপনার মাধ্যমে
                শিক্ষক উন্নয়নকে আরও এগিয়ে নিন
            </h2>

            <p class="mx-auto mt-4 max-w-2xl leading-7 text-emerald-50/80">
                শিক্ষক ও প্রশিক্ষণ সংক্রান্ত সকল কার্যক্রমকে একটি
                আধুনিক, দ্রুত ও নির্ভরযোগ্য প্ল্যাটফর্মে পরিচালনা করুন।
            </p>

            <div class="mt-8">

                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-white px-7 py-3.5 font-bold text-emerald-800 shadow-lg transition hover:bg-emerald-50">

                    সিস্টেমে প্রবেশ করুন

                    <span class="ml-2">→</span>

                </a>

            </div>

        </div>

    </div>

</section>


{{-- =========================
    FOOTER
========================== --}}
<footer id="contact" class="bg-slate-950 text-slate-300">

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">

        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

            <div>

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-700 text-white">
                        🎓
                    </div>

                    <div>
                        <div class="font-bold text-white">
                            জাতীয় বিশ্ববিদ্যালয়
                        </div>

                        <div class="text-xs text-slate-500">
                            শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা
                        </div>
                    </div>

                </div>

                <p class="mt-5 text-sm leading-7 text-slate-400">
                    শিক্ষক ব্যবস্থাপনা ও প্রশিক্ষণ কার্যক্রমকে
                    ডিজিটাল ও সমন্বিত করার একটি আধুনিক প্ল্যাটফর্ম।
                </p>

            </div>


            <div>

                <h3 class="font-bold text-white">
                    দ্রুত লিংক
                </h3>

                <ul class="mt-5 space-y-3 text-sm">

                    <li>
                        <a href="#about" class="hover:text-emerald-400">
                            আমাদের সম্পর্কে
                        </a>
                    </li>

                    <li>
                        <a href="#services" class="hover:text-emerald-400">
                            সেবাসমূহ
                        </a>
                    </li>

                    <li>
                        <a href="#training" class="hover:text-emerald-400">
                            প্রশিক্ষণ
                        </a>
                    </li>

                    <li>
                        <a href="#notices" class="hover:text-emerald-400">
                            নোটিশ
                        </a>
                    </li>

                </ul>

            </div>


            <div>

                <h3 class="font-bold text-white">
                    গুরুত্বপূর্ণ
                </h3>

                <ul class="mt-5 space-y-3 text-sm">

                    <li>
                        <a href="{{ route('login') }}" class="hover:text-emerald-400">
                            শিক্ষক লগইন
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('login') }}" class="hover:text-emerald-400">
                            প্রশাসনিক লগইন
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('login') }}" class="hover:text-emerald-400">
                            প্রশিক্ষণ ব্যবস্থাপনা
                        </a>
                    </li>

                </ul>

            </div>


            <div>

                <h3 class="font-bold text-white">
                    যোগাযোগ
                </h3>

                <ul class="mt-5 space-y-4 text-sm">

                    <li class="flex gap-3">
                        <span>📍</span>
                        <span>
                                জাতীয় বিশ্ববিদ্যালয়, বাংলাদেশ
                            </span>
                    </li>

                    <li class="flex gap-3">
                        <span>✉️</span>
                        <span>
                                info@example.edu.bd
                            </span>
                    </li>

                    <li class="flex gap-3">
                        <span>☎️</span>
                        <span>
                                +880 2 XXXX XXXX
                            </span>
                    </li>

                </ul>

            </div>

        </div>


        <div class="mt-12 border-t border-white/10 pt-7">

            <div class="flex flex-col justify-between gap-3 text-sm text-slate-500 sm:flex-row">

                <p>
                    © {{ date('Y') }} জাতীয় বিশ্ববিদ্যালয়। সর্বস্বত্ব সংরক্ষিত।
                </p>

                <p>
                    Teacher & Training Management System
                </p>

            </div>

        </div>

    </div>

</footer>

</body>

</html>

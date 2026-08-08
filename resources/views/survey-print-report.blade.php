<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>জরিপ মূল্যায়ন রিপোর্ট</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');
        body { font-family: 'Tiro Bangla', 'Kalpurush', serif; color: #1e293b; background-color: #fff; }

        /* প্রিন্ট করার সময় যেন ব্যাকগ্রাউন্ড কালার ঠিক থাকে */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { margin: 1cm; size: A4 portrait; }
        }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto" onload="window.print()">

<!-- বাটন (শুধু স্ক্রিনে দেখাবে, প্রিন্টে নয়) -->
<div class="no-print mb-6 flex justify-end">
    <button onclick="window.print()" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-indigo-700">
        🖨️ প্রিন্ট / সেভ PDF
    </button>
    <a href="{{ route('admin.survey.report') }}" class="ml-3 bg-slate-200 text-slate-800 px-5 py-2 rounded-lg font-semibold shadow hover:bg-slate-300">
        ফিরে যান
    </a>
</div>

<!-- রিপোর্ট হেডার -->
<div class="border-b-2 border-indigo-600 pb-6 mb-8 text-center">
    <h1 class="text-3xl font-bold text-slate-900 mb-2">জাতীয় বিশ্ববিদ্যালয়</h1>
    <h2 class="text-xl font-semibold text-indigo-700">প্রথম বর্ষের আইসিটি কোর্স: মতামত জরিপ ও মূল্যায়ন রিপোর্ট</h2>
    <p class="text-sm text-slate-500 mt-2">রিপোর্ট তৈরির তারিখ: {{ $date }}</p>
</div>

<!-- ১. নির্বাহী সারসংক্ষেপ -->
<div class="mb-8">
    <h3 class="text-xl font-bold bg-slate-100 p-2 border-l-4 border-indigo-600 mb-4">১. নির্বাহী সারসংক্ষেপ (Executive Summary)</h3>
    <p class="text-justify leading-relaxed mb-4">
        এই জরিপের মূল উদ্দেশ্য ছিল প্রথম বর্ষের আইসিটি কোর্স পরিচালনার ক্ষেত্রে শিক্ষক এবং শিক্ষার্থীদের বর্তমান প্রস্তুতি, মুখোমুখি হওয়া চ্যালেঞ্জ এবং ভবিষ্যৎ করণীয় সম্পর্কে একটি স্পষ্ট ধারণা পাওয়া। এই রিপোর্টের প্রাপ্ত ডেটা নীতিনির্ধারক ও কলেজ প্রশাসনকে কোর্সটি আরও কার্যকর করতে সুনির্দিষ্ট পদক্ষেপ নিতে সহায়তা করবে।
    </p>
    <div class="flex space-x-10 mt-4">
        <div class="bg-indigo-50 px-6 py-4 rounded-lg border border-indigo-100">
            <span class="block text-sm text-slate-500 font-semibold mb-1">মোট শিক্ষক অংশগ্রহণকারী</span>
            <span class="text-3xl font-bold text-indigo-700">{{ $totalTeachers }} জন</span>
        </div>
        <div class="bg-teal-50 px-6 py-4 rounded-lg border border-teal-100">
            <span class="block text-sm text-slate-500 font-semibold mb-1">মোট শিক্ষার্থী অংশগ্রহণকারী</span>
            <span class="text-3xl font-bold text-teal-700">{{ $totalStudents }} জন</span>
        </div>
    </div>
</div>

<!-- ২. মূল আবিষ্কার -->
<div class="mb-8">
    <h3 class="text-xl font-bold bg-slate-100 p-2 border-l-4 border-indigo-600 mb-4">২. মূল আবিষ্কার ও অ্যানালিটিক্স (Key Findings)</h3>

    <div class="mb-5">
        <h4 class="font-bold text-lg text-slate-800 mb-2">ক. শিক্ষক ও পাঠদান সংক্রান্ত মূল্যায়ন</h4>
        <ul class="list-disc pl-6 space-y-2 text-slate-700 text-justify">
            <li><strong>প্রস্তুতি ও কনফিডেন্স:</strong> অধিকাংশ শিক্ষক অনলাইন কোর্স (মুক্তপাঠ/P2E) এবং স্ব-অধ্যয়নের মাধ্যমে নিজেদের প্রস্তুত করেছেন। সঠিক গাইডলাইন পেলে তারা কোর্স পরিচালনায় আরও বেশি দক্ষ হয়ে উঠবেন বলে আত্মবিশ্বাস প্রকাশ করেছেন।</li>
            <li><strong>প্রধান চ্যালেঞ্জসমূহ:</strong> শিক্ষকদের পক্ষ থেকে পাওয়া তথ্যানুযায়ী, ল্যাব সুবিধার স্বল্পতা এবং ক্লাসে শিক্ষার্থীর সংখ্যা বেশি থাকা অন্যতম প্রধান অন্তরায় হিসেবে চিহ্নিত হয়েছে।</li>
        </ul>
    </div>

    <div>
        <h4 class="font-bold text-lg text-slate-800 mb-2">খ. শিক্ষার্থী ও লার্নিং মেথড সংক্রান্ত মূল্যায়ন</h4>
        <ul class="list-disc pl-6 space-y-2 text-slate-700 text-justify">
            <li><strong>ডিজিটাল জ্ঞান বৃদ্ধি:</strong> সিংহভাগ শিক্ষার্থী মতামত দিয়েছেন যে, এই কোর্সটি তাদের ডিজিটাল জ্ঞান বৃদ্ধিতে সহায়ক ভূমিকা পালন করেছে।</li>
            <li><strong>ব্যবহারিক ক্লাসের সংকট:</strong> পর্যাপ্ত কম্পিউটার ল্যাব না থাকা অথবা ল্যাব ব্যবহারের সুযোগ সীমিত থাকায় ব্যবহারিক ক্লাস সম্পন্ন করতে বেগ পোহাতে হয়েছে। অনেকে বিকল্প হিসেবে মোবাইল ফোন বা নিজ উদ্যোগে ল্যাপটপ ব্যবহার করেছেন।</li>
            <li><strong>পরীক্ষার প্রস্তুতি:</strong> শিক্ষার্থীরা পরীক্ষার প্রস্তুতির জন্য মূল ক্লাসের পাশাপাশি অনলাইন কোর্স এবং গাইডবইয়ের ওপর নির্ভর করেছেন।</li>
        </ul>
    </div>
</div>

<!-- ৩. সুপারিশ -->
<div class="mb-8">
    <h3 class="text-xl font-bold bg-slate-100 p-2 border-l-4 border-indigo-600 mb-4">৩. সিদ্ধান্ত গ্রহণের সুপারিশ (Actionable Recommendations)</h3>
    <ol class="list-decimal pl-6 space-y-3 text-slate-700 text-justify">
        <li><strong>ল্যাব সুবিধা ও অবকাঠামো উন্নয়ন:</strong> যেহেতু শিক্ষার্থীরা ল্যাব সংকট বা ল্যাব ব্যবহারে সুযোগ না পাওয়ার কথা বারবার উল্লেখ করেছেন, তাই কলেজ প্রশাসনকে প্রতিটি শিক্ষা প্রতিষ্ঠানে বাধ্যতামূলক কম্পিউটার ল্যাব সময়সূচি (Lab Schedule) নির্ধারণ করে দিতে হবে।</li>
        <li><strong>শিক্ষক প্রশিক্ষণ (In-House Training):</strong> শিক্ষকদের পাঠদান আরও সহজ করতে ইন-হাউজ ট্রেনিং এবং রিফ্রেশার কর্মশালার আয়োজন বাড়াতে হবে, যাতে তারা আধুনিক আইসিটি টুলস ক্লাসে সঠিকভাবে ব্যবহার করতে পারেন।</li>
        <li><strong>ডিজিটাল রিসোর্স প্রচার:</strong> শিক্ষার্থীরা নোটিশ ও আপডেটগুলো প্রধানত ওয়েবসাইট বা ফেসবুক পেজের মাধ্যমে পেতে চান। তাই কলেজগুলোর অফিশিয়াল সোশ্যাল মিডিয়া গ্রুপ বা পেজগুলোকে আরও সক্রিয় করতে হবে।</li>
        <li><strong>অতিরিক্ত লার্নিং ম্যাটেরিয়ালস:</strong> পরীক্ষার প্রস্তুতি সহজ করার জন্য অনলাইন কোর্স মডিউলগুলোর সাথে আরও শর্ট নোট বা ভিডিও টিউটোরিয়াল সংযুক্ত করার ব্যবস্থা করা যেতে পারে।</li>
    </ol>
</div>

<!-- স্বাক্ষর -->
<div class="mt-16 pt-8 border-t border-slate-200 flex justify-between">
    <div class="text-center">
        <div class="w-40 border-b border-slate-400 mb-2"></div>
        <p class="text-sm font-semibold">রিপোর্ট প্রস্তুতকারক</p>
    </div>
    <div class="text-center">
        <div class="w-40 border-b border-slate-400 mb-2"></div>
        <p class="text-sm font-semibold">অনুমোদনকারী কর্তৃপক্ষ</p>
    </div>
</div>

</body>
</html>

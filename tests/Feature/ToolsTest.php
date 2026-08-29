<?php

it('renders the tools directory and links it from public navigation', function () {
    $this->get(route('tools.index'))
        ->assertOk()
        ->assertSee('প্রয়োজনীয় অনলাইন টুলস')
        ->assertSee(route('tools.image-signature-compressor'), false)
        ->assertSee(route('tools.age-retirement-calculator'), false)
        ->assertSee(route('tools.cgpa-sgpa-calculator'), false)
        ->assertSee('ছবি ও স্বাক্ষর রিসাইজার');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('tools.index'), false)
        ->assertSee('টুলস');
});

it('renders the national university cgpa and sgpa calculator', function () {
    $this->get(route('tools.cgpa-sgpa-calculator'))
        ->assertOk()
        ->assertSee('data-cgpa-sgpa-calculator', false)
        ->assertSee('বর্তমান ফলাফল (SGPA)')
        ->assertSee('সর্বমোট ফলাফল (CGPA)')
        ->assertSee('গ্রেডিং স্কেল')
        ->assertSee('আরেকটি কোর্স যোগ করুন');
});

it('renders the age and retirement calculator', function () {
    $this->get(route('tools.age-retirement-calculator'))
        ->assertOk()
        ->assertSee('data-age-retirement-calculator', false)
        ->assertSee('জন্মতারিখ')
        ->assertSee('চাকরিতে যোগদানের তারিখ')
        ->assertSee('সম্ভাব্য অবসরের তারিখ')
        ->assertSee('প্রযোজ্য সরকারি বিধি');
});

it('renders the browser based image and signature compressor', function () {
    $this->get(route('tools.image-signature-compressor'))
        ->assertOk()
        ->assertSee('data-image-compressor', false)
        ->assertSee('data-image-input', false)
        ->assertSee('data-image-preview', false)
        ->assertSee('data-preview-stage', false)
        ->assertSee('data-reset-position', false)
        ->assertSee('data-zoom-in', false)
        ->assertSee('data-zoom-out', false)
        ->assertSee('data-zoom-value', false)
        ->assertSee('জুম ও অবস্থান রিসেট')
        ->assertSee('ছবিটি ড্রাগ করে অবস্থান ঠিক করুন')
        ->assertSee('data-local-only', false)
        ->assertSee('data-no-upload', false)
        ->assertSee('লাইভ প্রিভিউ')
        ->assertSee('ব্যাকগ্রাউন্ড')
        ->assertSee('রিমুভ করুন')
        ->assertSee('সলিড রঙ')
        ->assertSee('এক রঙের ব্যাকগ্রাউন্ডে ব্যবহার করুন')
        ->assertSee('একাধিক রঙ বা জটিল ব্যাকগ্রাউন্ড')
        ->assertSee('https://www.remove.bg/', false)
        ->assertSee('rel="noopener noreferrer"', false)
        ->assertSee('value="image/png"', false)
        ->assertSee('রিসাইজ ও কম্প্রেস করুন')
        ->assertSee('সার্ভারে সংরক্ষণ হবে না');
});

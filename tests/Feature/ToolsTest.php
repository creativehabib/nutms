<?php

it('renders the tools directory and links it from public navigation', function () {
    $this->get(route('tools.index'))
        ->assertOk()
        ->assertSee('প্রয়োজনীয় অনলাইন টুলস')
        ->assertSee(route('tools.image-signature-compressor'), false)
        ->assertSee(route('tools.age-retirement-calculator'), false)
        ->assertSee('ছবি ও স্বাক্ষর রিসাইজার');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('tools.index'), false)
        ->assertSee('টুলস');
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
        ->assertSee('data-local-only', false)
        ->assertSee('data-no-upload', false)
        ->assertSee('লাইভ প্রিভিউ')
        ->assertSee('রিসাইজ ও কম্প্রেস করুন')
        ->assertSee('সার্ভারে সংরক্ষণ হবে না');
});

<?php

it('renders the tools directory and links it from public navigation', function () {
    $this->get(route('tools.index'))
        ->assertOk()
        ->assertSee('প্রয়োজনীয় অনলাইন টুলস')
        ->assertSee(route('tools.image-signature-compressor'), false)
        ->assertSee('ছবি ও স্বাক্ষর রিসাইজার');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('tools.index'), false)
        ->assertSee('টুলস');
});

it('renders the browser based image and signature compressor', function () {
    $this->get(route('tools.image-signature-compressor'))
        ->assertOk()
        ->assertSee('data-image-compressor', false)
        ->assertSee('data-image-input', false)
        ->assertSee('data-image-preview', false)
        ->assertSee('রিসাইজ ও কম্প্রেস করুন')
        ->assertSee('ছবি কোনো সার্ভারে আপলোড হবে না');
});

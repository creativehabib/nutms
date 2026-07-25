<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('জাতীয় বিশ্ববিদ্যালয়')
        ->assertSee('শিক্ষক প্রশিক্ষণ দপ্তর')
        ->assertSee('শিক্ষক তথ্য ব্যবস্থাপনার সমন্বিত প্ল্যাটফর্ম')
        ->assertSee('ব্যবস্থাপনা সিস্টেমে প্রবেশ করুন');
});

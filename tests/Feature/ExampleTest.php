<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('জাতীয় বিশ্ববিদ্যালয়')
        ->assertSee('শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা')
        ->assertSee('ডিজিটাল শিক্ষক ও প্রশিক্ষণ ব্যবস্থাপনা প্ল্যাটফর্ম')
        ->assertSee('সিস্টেমে প্রবেশ করুন');
});

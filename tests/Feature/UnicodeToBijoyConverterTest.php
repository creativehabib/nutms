<?php

use App\Services\BanglaConverter;

it('renders the public unicode to bijoy converter', function () {
    $this->get(route('unicode-to-bijoy-converter'))
        ->assertOk()
        ->assertSee('ইউনিকোড টু বিজয় কনভার্টার')
        ->assertSee('data-unicode-bijoy-converter', false)
        ->assertSee('data-voice-typing', false)
        ->assertSee('data-convert="unicode-to-bijoy"', false)
        ->assertSee('data-convert="bijoy-to-unicode"', false)
        ->assertSee('ইউনিকোডে লেখা এখানে লিখুন অথবা পেস্ট করুন')
        ->assertSee('বিজয় কি-বোর্ডের লেখা এখানে লিখুন অথবা পেস্ট করুন')
        ->assertDontSee('readonly', false)
        ->assertSee('https://fonts.maateen.me/sutonny-mj/font.css', false)
        ->assertSee("font-family: 'SutonnyMJ'", false)
        ->assertSee('class="bijoy-text', false)
        ->assertSee('TXT ফাইল কনভার্টার')
        ->assertSee('data-theme-toggle', false);
});

it('links to the converter from the public navigation', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('unicode-to-bijoy-converter'), false)
        ->assertSee('কনভার্টার');
});

it('converts common Bangla text in both directions', function (string $unicode) {
    $bijoy = BanglaConverter::unicodeToBijoy($unicode);

    expect($bijoy)->not->toBe($unicode)
        ->and(BanglaConverter::bijoyToUnicode($bijoy))->toBe($unicode);
})->with([
    'simple sentence' => 'বাংলা ভাষা',
    'pre-kar' => 'কিশোর',
    'reph' => 'কর্ম',
    'ligature' => 'শিক্ষা',
    'digits' => '২০২৬।',
]);

it('keeps empty converter input empty', function () {
    expect(BanglaConverter::unicodeToBijoy(''))->toBe('')
        ->and(BanglaConverter::bijoyToUnicode(''))->toBe('');
});

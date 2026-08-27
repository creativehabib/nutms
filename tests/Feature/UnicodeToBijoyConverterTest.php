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

it('places every pre-kar before its Bijoy consonant glyph', function () {
    expect(BanglaConverter::unicodeToBijoy('কি কে কৈ কো কৌ'))
        ->toBe('wK †K ‰K †Kv †KŠ')
        ->and(BanglaConverter::unicodeToBijoy('আমার সোনার বাংলা আমি তোমায় ভালোবাসি!'))
        ->toBe('Avgvi †mvbvi evsjv Avwg †Zvgvq fv†jvevwm!');
});

it('round trips compound vowels, rephs and common conjuncts without broken glyphs', function () {
    $unicode = 'সৌন্দর্য যৌথ কৌশল সম্পন্ন প্রজ্ঞা শ্রদ্ধা বন্ধু লক্ষ্মী';
    $bijoy = BanglaConverter::unicodeToBijoy($unicode);

    expect($bijoy)->not->toContain('&')
        ->and(BanglaConverter::bijoyToUnicode($bijoy))->toBe($unicode);
});

it('converts the public page titles without leaving raw hasants', function () {
    $unicode = "অধিভুক্ত কলেজ ডিরেক্টরি\nইউনিকোড টু বিজয় কনভার্টার";
    $bijoy = BanglaConverter::unicodeToBijoy($unicode);

    expect($bijoy)->toContain('³')
        ->toContain('±')
        ->not->toContain('&')
        ->and(BanglaConverter::bijoyToUnicode($bijoy))->toBe($unicode);
});

it('repairs alternate Bijoy glyphs used by legacy documents', function () {
    $legacyBijoy = '†iwR÷ªvi RvZxq wek^we`¨vjq wkÿK cÖwkÿY Kzgvi c~e©v‡ý';

    expect(BanglaConverter::bijoyToUnicode($legacyBijoy))
        ->toBe('রেজিস্ট্রার জাতীয় বিশ্ববিদ্যালয় শিক্ষক প্রশিক্ষণ কুমার পূর্বাহ্ণে');
});

it('repairs mixed legacy symbols left in otherwise converted text', function () {
    expect(BanglaConverter::bijoyToUnicode('খিª রেজিস্টªার বিশ^বিদ¨ালয় শিÿক কzমার'))
        ->toBe('খ্রি রেজিস্ট্রার বিশ্ববিদ্যালয় শিক্ষক কুমার');
});

it('keeps empty converter input empty', function () {
    expect(BanglaConverter::unicodeToBijoy(''))->toBe('')
        ->and(BanglaConverter::bijoyToUnicode(''))->toBe('');
});

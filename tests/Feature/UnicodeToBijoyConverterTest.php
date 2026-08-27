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
        ->toBe('Avgvi †mvbvi evsjv Avwg †Zvgvq fv‡jvevwm!');
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

it('uses legacy fola glyphs instead of raw hasants in official documents', function () {
    $unicode = 'খ্রিঃ রেজিস্ট্রার বিশ্ববিদ্যালয় মাধ্যম অদ্য পূর্বাহ্ণে টেকনিক্যাল';
    $bijoy = BanglaConverter::unicodeToBijoy($unicode);

    expect($bijoy)->not->toContain('&')
        ->and($bijoy)->toContain('ª')
        ->and($bijoy)->toContain('¨')
        ->and($bijoy)->toContain('ý')
        ->and(BanglaConverter::bijoyToUnicode($bijoy))->toBe($unicode);
});

it('keeps the conjunct and pre-kar intact when converting কেন্দ্র', function () {
    expect(BanglaConverter::unicodeToBijoy('আঞ্চলিক কেন্দ্র থেকে বদলী'))
        ->toBe('AvÂwjK †K›`ª †_‡K e`jx')
        ->and(BanglaConverter::bijoyToUnicode('AvÂwjK †K›`ª †_‡K e`jx'))
        ->toBe('আঞ্চলিক কেন্দ্র থেকে বদলী');
});

it('removes invisible joiners before converting মোতাবেক and পূর্বাহ্নে', function () {
    $textWithJoiners = "মে\u{200D}াতাবেক পূ\u{200D}\u{200D}র্বাহ্নে";
    $bijoy = BanglaConverter::unicodeToBijoy($textWithJoiners);

    expect($bijoy)->toBe('†gvZv‡eK c~e©v‡nè')
        ->not->toContain("\u{200D}")
        ->and(BanglaConverter::bijoyToUnicode($bijoy))->toBe('মোতাবেক পূর্বাহ্নে');
});

it('uses the medial e-kar glyph without detaching the matra', function () {
    expect(BanglaConverter::unicodeToBijoy('মোতাবেক দপ্তরে আমাকে লক্ষ্যে'))
        ->toBe('†gvZv‡eK `ß‡i Avgv‡K j‡¶¨')
        ->and(BanglaConverter::bijoyToUnicode('†gvZv‡eK `ß‡i Avgv‡K j‡¶¨'))
        ->toBe('মোতাবেক দপ্তরে আমাকে লক্ষ্যে');
});

it('resolves reph before moving pre-kar in মর্জিত', function () {
    expect(BanglaConverter::bijoyToUnicode('Avcbvi gwR©Z Kvgbv KiwQ'))
        ->toBe('আপনার মর্জিত কামনা করছি')
        ->and(BanglaConverter::bijoyToUnicode('মজি©ত'))->toBe('মর্জিত');
});

it('normalizes a spaced Bijoy visarga as a punctuation colon', function () {
    expect(BanglaConverter::bijoyToUnicode("mshyw³ t\nwelq t †hvM`vb"))
        ->toBe("সংযুক্তি :\nবিষয় : যোগদান")
        ->and(BanglaConverter::bijoyToUnicode('`ytL'))->toBe('দুঃখ')
        ->and(BanglaConverter::bijoyToUnicode('wLªt'))->toBe('খ্রিঃ');
});

it('converts the alternate ri-kar glyph in শৃঙ্খলা', function () {
    expect(BanglaConverter::bijoyToUnicode('k„•Ljv'))->toBe('শৃঙ্খলা')
        ->and(BanglaConverter::bijoyToUnicode('শ„ঙ্খলা'))->toBe('শৃঙ্খলা');
});

it('keeps empty converter input empty', function () {
    expect(BanglaConverter::unicodeToBijoy(''))->toBe('')
        ->and(BanglaConverter::bijoyToUnicode(''))->toBe('');
});

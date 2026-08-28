import assert from 'node:assert/strict';
import test from 'node:test';

import { convertBijoyToUnicode, convertUnicodeToBijoy, getBijoyClipboardValue, normalizeUnicodeBangla } from '../../resources/js/bangla-converter.js';

const conversionCases = [
    ['আন্তর্জাতিক', 'AvšÍR©vwZK'],
    ['সিদ্ধান্ত', 'wm×všÍ'],
    ['পর্যন্ত', 'ch©šÍ'],
];

test('converts reph and nasal conjunct words without breaking Bijoy glyph sequences', () => {
    for (const [unicode, bijoy] of conversionCases) {
        assert.equal(convertUnicodeToBijoy(unicode), bijoy);
        assert.equal(convertBijoyToUnicode(bijoy), unicode);
    }
});

test('normalizes invisible joiners before conversion', () => {
    assert.equal(convertUnicodeToBijoy("প\u200D\u200Dর্যন্ত"), 'ch©šÍ');
});

test('converts the reported phrase as a complete value', () => {
    const unicode = 'আন্তর্জাতিক, সিদ্ধান্ত, পর্যন্ত';
    const bijoy = 'AvšÍR©vwZK, wm×všÍ, ch©šÍ';

    assert.equal(convertUnicodeToBijoy(unicode), bijoy);
    assert.equal(convertBijoyToUnicode(bijoy), unicode);
});

test('provides a normalized readable preview without changing the copied Bijoy value', () => {
    const unicodeWithJoiners = "ইউনিকোড লেখা বিজয়ে রূপান্তর হ‍য়েছে।";

    assert.equal(normalizeUnicodeBangla(unicodeWithJoiners), 'ইউনিকোড লেখা বিজয়ে রূপান্তর হয়েছে।');
    assert.equal(convertUnicodeToBijoy(unicodeWithJoiners), 'BDwb‡KvW †jLv weR‡q iƒcvšÍi n‡q‡Q|');
});

test('copies the stored Bijoy payload instead of the readable preview', () => {
    assert.equal(
        getBijoyClipboardValue('ইউনিকোড লেখা', 'BDwb‡KvW †jLv'),
        'BDwb‡KvW †jLv',
    );
    assert.equal(getBijoyClipboardValue('BDwb‡KvW †jLv'), 'BDwb‡KvW †jLv');
    assert.equal(convertUnicodeToBijoy('ইউনিকোড'), 'BDwb‡KvW');
});

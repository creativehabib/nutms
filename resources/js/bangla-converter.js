const unicodeToBijoyCharacters = new Map([
    ['অ', 'A'], ['আ', 'Av'], ['ই', 'B'], ['ঈ', 'C'], ['উ', 'D'], ['ঊ', 'E'], ['ঋ', 'F'], ['এ', 'G'], ['ঐ', 'H'], ['ও', 'I'], ['ঔ', 'J'],
    ['ক', 'K'], ['খ', 'L'], ['গ', 'M'], ['ঘ', 'N'], ['ঙ', 'O'], ['চ', 'P'], ['ছ', 'Q'], ['জ', 'R'], ['ঝ', 'S'], ['ঞ', 'T'],
    ['ট', 'U'], ['ঠ', 'V'], ['ড', 'W'], ['ঢ', 'X'], ['ণ', 'Y'], ['ত', 'Z'], ['থ', '_'], ['দ', '`'], ['ধ', 'a'], ['ন', 'b'],
    ['প', 'c'], ['ফ', 'd'], ['ব', 'e'], ['ভ', 'f'], ['ম', 'g'], ['য', 'h'], ['র', 'i'], ['ল', 'j'], ['শ', 'k'], ['ষ', 'l'], ['স', 'm'], ['হ', 'n'], ['ড়', 'o'], ['ঢ়', 'p'], ['য়', 'q'], ['ৎ', 'r'],
    ['ং', 's'], ['ঃ', 't'], ['ঁ', 'u'], ['া', 'v'], ['ি', 'w'], ['ী', 'x'], ['ু', 'y'], ['ূ', '~'], ['ৃ', '…'], ['ে', '†'], ['ৈ', '‰'], ['ো', '‡'], ['ৌ', 'Š'], ['ৗ', 'Š'], ['্', '&'],
    ['০', '0'], ['১', '1'], ['২', '2'], ['৩', '3'], ['৪', '4'], ['৫', '5'], ['৬', '6'], ['৭', '7'], ['৮', '8'], ['৯', '9'], ['।', '|'], ['–', '–'], ['—', '—'],
]);

const unicodeToBijoyLigatures = new Map([
    ['ক্ষ্ম', '²'], ['ক্ক', '°'], ['ক্ট', '±'], ['ক্ত', '³'], ['ক্ব', 'K¡'], ['ক্ম', 'K¥'], ['ক্ল', 'K¬'], ['ক্স', 'K·'], ['ক্ষ', '¶'], ['জ্ঞ', 'Á'], ['ঙ্ক', '¼'], ['ঙ্খ', '•L'], ['ঙ্গ', '½'], ['ঙ্ঘ', '•N'], ['চ্চ', '”P'], ['চ্ছ', '”Q'], ['জ্জ', '¾'], ['ঞ্ছ', 'TQ'], ['ঞ্চ', 'Â'], ['ঞ্জ', 'Ã'],
    ['ট্ট', 'Æ'], ['ড্ড', 'Ç'], ['ণ্ট', 'È'], ['ণ্ঠ', 'É'], ['ণ্ড', 'Ê'], ['ত্ত', 'Ë'], ['ত্থ', 'Ì'], ['ত্ম', 'Z¥'], ['ত্র', 'Î'], ['দ্দ', 'Ï'], ['দ্ধ', '×'], ['দ্ব', 'Ø'], ['দ্ম', '`¥'], ['দ্র', '`ª'],
    ['ধ্ব', 'aŸ'], ['ন্ত্র', 'šÍª'], ['ন্দ্র', '›`ª'], ['ন্ত', 'šÍ'], ['ন্থ', 'š’'], ['ন্ট', '›U'], ['ন্ড', 'Û'], ['ন্দ', '›`'], ['ন্ধ', 'Ü'], ['ন্ন', 'bœ'], ['ন্ব', 'š^'], ['ন্ম', 'š§'], ['প্ত', 'ß'], ['প্প', 'à'], ['প্ল', 'cø'], ['ব্জ', 'â'], ['ব্দ', 'ã'], ['ব্ধ', 'ä'],
    ['ব্ব', 'eŸ'], ['ব্ল', 'eø'], ['ভ্র', 'å'], ['ম্ন', 'gœ'], ['ম্প্র', '¤úª'], ['ম্প', '¤ú'], ['ম্ফ', 'ç'], ['ম্ব', '¤^'], ['ম্ভ', '¤¢'], ['ম্ম', '¤§'], ['ম্ল', 'gø'], ['ল্ক', 'é'], ['ল্গ', 'ê'], ['ল্ট', 'ë'], ['ল্ড', 'ì'],
    ['ল্প', 'í'], ['ল্ফ', 'î'], ['ল্ব', 'j¦'], ['ল্ম', 'j¥'], ['ল্ল', 'jø'], ['শ্চ', 'ð'], ['শ্ন', 'kœ'], ['শ্ব', 'k¦'], ['শ্ম', 'k¥'], ['শ্ল', 'kø'], ['ষ্ক', '®‹'], ['ষ্ট্র', 'óª'], ['ষ্ট', 'ó'], ['ষ্ঠ', 'ô'], ['ষ্প', '®ú'], ['ষ্ফ', 'õ'],
    ['ষ্ম', '®§'], ['স্ক', '¯‹'], ['স্ট্র', '÷ª'], ['স্ট', '÷'], ['স্ত', '¯Í'], ['স্থ', '¯’'], ['স্ন', 'mœ'], ['স্প', '¯ú'], ['স্ফ', 'ù'], ['স্ব', '¯^'], ['স্ম', '¯§'], ['স্ল', 'mø'], ['হ্ন', 'nè'], ['হ্ব', 'nŸ'], ['হ্ম', 'þ'], ['হ্ল', 'n¬'],
    ['ক্র', 'µ'], ['গ্র', 'MÖ'], ['প্র', 'cÖ'], ['ব্র', 'eª'], ['শ্র', 'kÖ'], ['রু', 'i“'], ['রূ', 'iƒ'],
]);

const bijoyToUnicodeLigatures = new Map([...unicodeToBijoyLigatures].map(([unicode, bijoy]) => [bijoy, unicode]));
const bijoyToUnicodeCharacters = new Map([...unicodeToBijoyCharacters].map(([unicode, bijoy]) => [bijoy, unicode]));
const bijoyToUnicodeAliases = new Map([
    ['‡', 'ে'], ['ª', '্র'], ['¨', '্য'], ['^', '্ব'], ['ÿ', 'ক্ষ'], ['ý', 'হ্ণ'], ['„', 'ৃ'], ['z', 'ু'],
]);
const unicodeToBijoyFallbacks = new Map([
    ['হ্ণ', 'ý'], ['্র', 'ª'], ['্য', '¨'], ['্ব', '^'],
]);
const unicodeToBijoyPunctuation = new Map([
    ['“', 'Ò'], ['”', 'Ó'], ['‘', 'Ô'], ['’', 'Õ'],
]);
const bijoyToUnicodePunctuation = new Map([...unicodeToBijoyPunctuation].map(([unicode, bijoy]) => [bijoy, unicode]));

const replaceFromMap = (value, replacements) => [...replacements.entries()]
    .sort(([first], [second]) => second.length - first.length)
    .reduce((result, [search, replacement]) => result.split(search).join(replacement), value);

export const normalizeUnicodeBangla = (value) => value
    .replace(/[\u200B-\u200D\u2060\uFEFF]/g, '')
    .normalize('NFC')
    .replace(/ড়/g, 'ড়')
    .replace(/ঢ়/g, 'ঢ়')
    .replace(/য়/g, 'য়')
    .replace(/ো/g, 'ো')
    .replace(/ৌ/g, 'ৌ')
    .replace(/[\t ]+ঃ(?=\s|$)/g, ' :')
    .replace(/্{2,}/g, '্');

export const convertUnicodeToBijoy = (value) => {
    let converted = normalizeUnicodeBangla(value)
        .replace(/ো/g, 'ো')
        .replace(/ৌ/g, 'ৌ')
        .replace(/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)([িেৈ])/g, '$2$1')
        .replace(/র্([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)/g, '$1©');

    converted = replaceFromMap(converted, unicodeToBijoyPunctuation);
    converted = replaceFromMap(converted, unicodeToBijoyLigatures);
    converted = replaceFromMap(converted, unicodeToBijoyFallbacks);
    converted = replaceFromMap(converted, unicodeToBijoyCharacters);

    return converted.replace(/([A-Za-z0-9_`\u00A1-\uFFFF])†/g, '$1‡');
};

export const convertBijoyToUnicode = (value) => {
    if (/[\u0980-\u09FF]/.test(value)) {
        return normalizeUnicodeBangla(replaceFromMap(replaceFromMap(value, bijoyToUnicodePunctuation), bijoyToUnicodeAliases)
            .replace(/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)([িেৈ])©/g, 'র্$1$2')
            .replace(/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)©/g, 'র্$1')
            .replace(/([ক-হড়ঢ়য়ৎ])([িেৈ])((?:্[রযব])+)/g, '$1$3$2'));
    }

    let converted = replaceFromMap(value, bijoyToUnicodeLigatures);
    converted = replaceFromMap(converted, bijoyToUnicodePunctuation);
    converted = replaceFromMap(converted, bijoyToUnicodeAliases);
    converted = replaceFromMap(converted, bijoyToUnicodeCharacters)
        .replace(/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)©/g, 'র্$1')
        .replace(/([িেৈোৌ])([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)/g, '$2$1')
        .replace(/([ক-হড়ঢ়য়ৎ])([িেৈ])((?:্[রযব])+)/g, '$1$3$2');

    return normalizeUnicodeBangla(converted);
};

export const getBijoyClipboardValue = (visibleValue, storedBijoyValue = '') => storedBijoyValue || visibleValue;

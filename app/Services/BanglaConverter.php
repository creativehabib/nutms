<?php

namespace App\Services;

final class BanglaConverter
{
    /** @var array<string, string> */
    private const UNICODE_TO_BIJOY_CHARACTERS = [
        'অ' => 'A', 'আ' => 'Av', 'ই' => 'B', 'ঈ' => 'C', 'উ' => 'D', 'ঊ' => 'E', 'ঋ' => 'F', 'এ' => 'G', 'ঐ' => 'H', 'ও' => 'I', 'ঔ' => 'J',
        'ক' => 'K', 'খ' => 'L', 'গ' => 'M', 'ঘ' => 'N', 'ঙ' => 'O', 'চ' => 'P', 'ছ' => 'Q', 'জ' => 'R', 'ঝ' => 'S', 'ঞ' => 'T',
        'ট' => 'U', 'ঠ' => 'V', 'ড' => 'W', 'ঢ' => 'X', 'ণ' => 'Y', 'ত' => 'Z', 'থ' => '_', 'দ' => '`', 'ধ' => 'a', 'ন' => 'b',
        'প' => 'c', 'ফ' => 'd', 'ব' => 'e', 'ভ' => 'f', 'ম' => 'g', 'য' => 'h', 'র' => 'i', 'ল' => 'j', 'শ' => 'k', 'ষ' => 'l', 'স' => 'm', 'হ' => 'n',
        'ড়' => 'o', 'ঢ়' => 'p', 'য়' => 'q', 'ৎ' => 'r', 'ং' => 's', 'ঃ' => 't', 'ঁ' => 'u', 'া' => 'v', 'ি' => 'w', 'ী' => 'x', 'ু' => 'y', 'ূ' => '~',
        'ৃ' => '…', 'ে' => '†', 'ৈ' => '‰', 'ো' => '‡', 'ৌ' => 'Š', 'ৗ' => 'Š', '্' => '&', '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
        '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9', '।' => '|',
    ];

    /** @var array<string, string> */
    private const UNICODE_TO_BIJOY_LIGATURES = [
        'ক্ষ্ম' => '²', 'ক্ক' => '°', 'ক্ট' => '±', 'ক্ত' => '³', 'ক্ব' => 'K¡', 'ক্ম' => 'K¥', 'ক্ল' => 'K¬', 'ক্স' => 'K·', 'ক্ষ' => '¶', 'জ্ঞ' => 'Á', 'ঙ্ক' => '¼', 'ঙ্খ' => '•L', 'ঙ্গ' => '½', 'ঙ্ঘ' => '•N', 'চ্চ' => '”P', 'চ্ছ' => '”Q', 'জ্জ' => '¾', 'ঞ্ছ' => 'TQ', 'ঞ্চ' => 'Â', 'ঞ্জ' => 'Ã',
        'ট্ট' => 'Æ', 'ড্ড' => 'Ç', 'ণ্ট' => 'È', 'ণ্ঠ' => 'É', 'ণ্ড' => 'Ê', 'ত্ত' => 'Ë', 'ত্থ' => 'Ì', 'ত্ম' => 'Z¥', 'ত্র' => 'Î', 'দ্দ' => 'Ï', 'দ্ধ' => '×', 'দ্ব' => 'Ø', 'দ্ম' => '`¥', 'দ্র' => '`ª',
        'ধ্ব' => 'aŸ', 'ন্ত' => 'šÍ', 'ন্থ' => 'š’', 'ন্দ' => '›`', 'ন্ধ' => 'Ü', 'ন্ন' => 'bœ', 'ন্ব' => 'š^', 'ন্ম' => 'š§', 'প্ত' => 'ß', 'প্প' => 'à', 'প্ল' => 'cø', 'ব্জ' => 'â', 'ব্দ' => 'ã', 'ব্ধ' => 'ä',
        'ব্ব' => 'eŸ', 'ব্ল' => 'eø', 'ভ্র' => 'å', 'ম্ন' => 'gœ', 'ম্প' => '¤ú', 'ম্ফ' => 'ç', 'ম্ব' => '¤^', 'ম্ভ' => '¤¢', 'ম্ম' => '¤§', 'ম্ল' => 'gø', 'ল্ক' => 'é', 'ল্গ' => 'ê', 'ল্ট' => 'ë', 'ল্ড' => 'ì',
        'ল্প' => 'í', 'ল্ফ' => 'î', 'ল্ব' => 'j¦', 'ল্ম' => 'j¥', 'ল্ল' => 'jø', 'শ্চ' => 'ð', 'শ্ন' => 'kœ', 'শ্ব' => 'k¦', 'শ্ম' => 'k¥', 'শ্ল' => 'kø', 'ষ্ক' => '®‹', 'ষ্ট' => 'ó', 'ষ্ঠ' => 'ô', 'ষ্প' => '®ú', 'ষ্ফ' => 'õ',
        'ষ্ম' => '®§', 'স্ক' => '¯‹', 'স্ট' => '÷', 'স্ত' => '¯Í', 'স্থ' => '¯’', 'স্ন' => 'mœ', 'স্প' => '¯ú', 'স্ফ' => 'ù', 'স্ব' => '¯^', 'স্ম' => '¯§', 'স্ল' => 'mø', 'হ্ন' => 'nè', 'হ্ব' => 'nŸ', 'হ্ম' => 'þ', 'হ্ল' => 'n¬',
        'ক্র' => 'µ', 'গ্র' => 'MÖ', 'প্র' => 'cÖ', 'ব্র' => 'eª', 'শ্র' => 'kÖ', 'রু' => 'i“', 'রূ' => 'iƒ',
    ];

    public static function bijoyToUnicode(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = self::replaceLongestFirst($text, array_flip(self::UNICODE_TO_BIJOY_LIGATURES));
        $text = self::replaceLongestFirst($text, array_flip(self::UNICODE_TO_BIJOY_CHARACTERS));
        $text = preg_replace('/([িেৈোৌ])([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)/u', '$2$1', $text) ?? $text;
        $text = preg_replace('/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)©/u', 'র্$1', $text) ?? $text;

        return self::normalizeUnicode($text);
    }

    public static function unicodeToBijoy(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = self::decomposeUnicodeKar(self::normalizeUnicode($text));
        $text = preg_replace('/([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)([িেৈ])/u', '$2$1', $text) ?? $text;
        $text = preg_replace('/র্([ক-হড়ঢ়য়ৎ](?:্[ক-হড়ঢ়য়ৎ])*)/u', '$1©', $text) ?? $text;
        $text = self::replaceLongestFirst($text, self::UNICODE_TO_BIJOY_LIGATURES);

        return self::replaceLongestFirst($text, self::UNICODE_TO_BIJOY_CHARACTERS);
    }

    private static function normalizeUnicode(string $text): string
    {
        return str_replace(['ো', 'ৌ'], ['ো', 'ৌ'], $text);
    }

    private static function decomposeUnicodeKar(string $text): string
    {
        return str_replace(['ো', 'ৌ'], ['ো', 'ৌ'], $text);
    }

    /** @param array<string, string> $replacements */
    private static function replaceLongestFirst(string $text, array $replacements): string
    {
        uksort($replacements, fn (string $first, string $second): int => mb_strlen($second) <=> mb_strlen($first));

        return strtr($text, $replacements);
    }
}

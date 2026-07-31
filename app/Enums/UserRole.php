<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Principal = 'principal';
    case Teacher = 'teacher';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'এডমিন',
            self::Principal => 'কলেজ প্রিন্সিপাল',
            self::Teacher => 'শিক্ষক',
        };
    }

    /** @return array<int, string> */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => ['সকল কলেজ ও শিক্ষক ব্যবস্থাপনা', 'রোল পরিবর্তন', 'রেফারেন্স ও ট্রেনিং ক্যাটালগ', 'সকল রিপোর্ট'],
            self::Principal => ['নিজ কলেজ প্রোফাইল সম্পাদনা', 'নিজ কলেজের শিক্ষক ব্যবস্থাপনা', 'শিক্ষক প্রোফাইল অনুমোদন'],
            self::Teacher => ['নিজ শিক্ষক প্রোফাইল তৈরি, দেখা ও সম্পাদনা'],
        };
    }
}

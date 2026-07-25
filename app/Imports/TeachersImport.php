<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TeachersImport implements ToCollection, WithStartRow, WithChunkReading
{
    protected $collegeName;

    // কনস্ট্রাক্টরের মাধ্যমে ফাইলের নাম থেকে পাওয়া কলেজের নাম রিসিভ করা হচ্ছে
    public function __construct($collegeName = null)
    {
        $this->collegeName = $collegeName;
    }

    public function startRow(): int
    {
        return 2; // হেডিং স্কিপ করার জন্য
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // ফাইলের ধরন অনুযায়ী কলামের ইনডেক্স ডাইনামিক্যালি বের করার লজিক
            $offset = 0;

            // আমরা চেক করব মোবাইল ও ইমেইলের ডেটা ১৮ নাকি ১৯ নম্বর ইনডেক্সে আছে
            $contactStr18 = (string) ($row[18] ?? '');
            $contactStr19 = (string) ($row[19] ?? '');

            if (str_contains($contactStr19, '@') || preg_match('/[0-9]{11}/', $contactStr19)) {
                $offset = 1; // আগের ফাইলের মতো যদি ১ ঘর সরে থাকে
            } elseif (str_contains($contactStr18, '@') || preg_match('/[0-9]{11}/', $contactStr18)) {
                $offset = 0; // নতুন ফাইলের মতো যদি শুরু থেকেই থাকে
            } else {
                // কোনো কারণে ইমেইল না থাকলে প্রথম কলাম ফাঁকা কি না তা দেখে যাচাই করা
                $offset = empty($row[0]) ? 1 : 0;
            }

            // ডাইনামিক অফসেট (offset) দিয়ে ডেটা বের করা
            $tmisId = $row[2 + $offset] ?? null;
            $name   = $row[4 + $offset] ?? null;

            if (!$tmisId && !$name) {
                continue; // ফাঁকা রো স্কিপ করে লুপের পরের লাইনে চলে যাবে
            }

            $contactInfo = $row[18 + $offset] ?? '';
            $mobile = null;
            $email = null;

            if ($contactInfo) {
                // ইমেইল এবং মোবাইল স্পেস বা লাইন-ব্রেক দিয়ে আলাদা করা
                $parts = preg_split('/[\s\r\n]+/', $contactInfo);
                $mobile = trim($parts[1] ?? (preg_match('/[0-9]{11}/', $parts[0]) ? $parts[0] : ''));
                $email = trim($parts[0] ?? (str_contains($parts[1] ?? '', '@') ? $parts[1] : ''));
            }

            // কম্পিউটার কাউন্টে স্ট্রিং (Text) থাকলে সেটি যেন ডেটাবেস ক্র্যাশ না করে
            $computerCount = $row[17 + $offset] ?? null;
            if (!is_numeric($computerCount)) {
                $computerCount = null;
            }

            $data = [
                'college_code'            => $row[0 + $offset] ?? null,
                'college_name'            => $this->collegeName,
                'ttis_id'                 => $row[3 + $offset] ?? null,
                'name'                    => $name,
                'designation'             => $row[5 + $offset] ?? null,
                'subject'                 => $row[6 + $offset] ?? null,
                'teacher_level'           => $row[7 + $offset] ?? null,
                'employment_type'         => $row[8 + $offset] ?? null,
                'has_training'            => $row[9 + $offset] ?? null,
                'ict_training_name'       => $row[10 + $offset] ?? null,
                'ict_training_duration'   => $row[11 + $offset] ?? null,
                'other_training_name'     => $row[12 + $offset] ?? null,
                'other_training_duration' => $row[13 + $offset] ?? null,
                'training_institute'      => $row[14 + $offset] ?? null,
                'training_year'           => $row[15 + $offset] ?? null,
                'has_computer_lab'        => $row[16 + $offset] ?? null,
                'computer_count'          => $computerCount,
                'mobile_number'           => $mobile,
                'email'                   => $email,
            ];

            // ডেটা সেভ বা আপডেট করা
            if ($tmisId) {
                Teacher::updateOrCreate(['tmis_id' => $tmisId], $data);
            } else {
                Teacher::updateOrCreate(
                    [
                        'name' => $name,
                        'subject' => $data['subject'],
                        'college_name' => $this->collegeName,
                    ],
                    $data
                );
            }
        }
    }

    // মেমরি ও স্পিড অপটিমাইজেশনের জন্য Chunk যুক্ত করা হলো
    public function chunkSize(): int
    {
        return 500;
    }
}

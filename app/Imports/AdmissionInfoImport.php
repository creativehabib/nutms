<?php

namespace App\Imports;

use App\Models\AdmissionInfo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdmissionInfoImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // যদি এক্সেলের কোনো রো-তে college_code বা subject_id না থাকে, তবে সেটি স্কিপ করবে
            if (empty($row['college_code']) || empty($row['subject_id'])) {
                continue;
            }

            // updateOrCreate ব্যবহার করে ডুপ্লিকেট ঠেকানো এবং আপডেট করা
            AdmissionInfo::updateOrCreate(
                [
                    // ১. Matching Conditions: এই দুটি কলাম দিয়ে ডাটাবেজে চেক করা হবে ডেটা আছে কি না
                    'college_code' => $row['college_code'],
                    'subject_id' => $row['subject_id'],
                ],
                [
                    // ২. Update/Insert Data: যদি মিলে যায় তবে এগুলো আপডেট হবে, না মিললে নতুন সেভ হবে
                    'division' => $row['division'],
                    'district' => $row['district'],
                    'college_name' => $row['college_name'],
                    'category' => $row['category'] ?? null,
                    'subject_name' => $row['subject_name'],
                    'sess_21_22_total_admited' => $row['sess_21_22_total_admited'] ?? 0,
                    'sess_22_23_total_admited' => $row['sess_22_23_total_admited'] ?? 0,
                    'sess_23_24_total_admited' => $row['sess_23_24_total_admited'] ?? 0,
                    'sess_24_25_total_admited' => $row['sess_24_25_total_admited'] ?? 0,
                ]
            );
        }
    }

    // মেমরি লিমিট এড়ানোর জন্য চাঙ্ক রিডিং (একসাথে ৫০০ রো পড়বে)
    public function chunkSize(): int
    {
        return 1000;
    }
}

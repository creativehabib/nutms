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

            // ১. ডাটাবেস থেকে existing ডেটা খুঁজে বের করা অথবা নতুন ইন্সট্যান্স তৈরি করা
            $record = AdmissionInfo::firstOrNew([
                'college_code' => $row['college_code'],
                'subject_id' => $row['subject_id'],
            ]);

            // ২. সাধারণ তথ্যগুলো এক্সেল থেকে সেট করা
            $record->division = $row['division'];
            $record->district = $row['district'];
            $record->college_name = $row['college_name'];
            $record->category = $row['category'] ?? null;
            $record->subject_name = $row['subject_name'];

            // ৩. ম্যাজিক লজিক: ডাটাবেসের বর্তমান মান এবং এক্সেলের নতুন মানের মধ্যে যেটি বড় (MAX), সেটিই সেভ হবে!
            // এতে করে ০ এসে কখনোই আগের ৬ বা ২৪-কে মুছে দিতে পারবে না।
            $record->sess_21_22_total_admited = max((int) $record->sess_21_22_total_admited, (int) ($row['sess_21_22_total_admited'] ?? 0));
            $record->sess_22_23_total_admited = max((int) $record->sess_22_23_total_admited, (int) ($row['sess_22_23_total_admited'] ?? 0));
            $record->sess_23_24_total_admited = max((int) $record->sess_23_24_total_admited, (int) ($row['sess_23_24_total_admited'] ?? 0));
            $record->sess_24_25_total_admited = max((int) $record->sess_24_25_total_admited, (int) ($row['sess_24_25_total_admited'] ?? 0));

            $record->save();
        }
    }

    // মেমরি লিমিট এড়ানোর জন্য চাঙ্ক রিডিং (একসাথে ১০০০ রো পড়বে)
    public function chunkSize(): int
    {
        return 1000;
    }
}

<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['subject_code' => '1001', 'name' => 'Bangla'],
            ['subject_code' => '1101', 'name' => 'English'],
            ['subject_code' => '1201', 'name' => 'Arabic'],
            ['subject_code' => '1301', 'name' => 'Pali'],
            ['subject_code' => '1401', 'name' => 'Sanskrit'],
            ['subject_code' => '1501', 'name' => 'History'],
            ['subject_code' => '1601', 'name' => 'Islamic History & Culture'],
            ['subject_code' => '1701', 'name' => 'Philosophy'],
            ['subject_code' => '1801', 'name' => 'Islamic Studies'],
            ['subject_code' => '1901', 'name' => 'Political Science'],
            ['subject_code' => '2001', 'name' => 'Sociology'],
            ['subject_code' => '2101', 'name' => 'Social Work'],
            ['subject_code' => '2201', 'name' => 'Economics'],
            ['subject_code' => '2301', 'name' => 'Marketing'],
            ['subject_code' => '2401', 'name' => 'Finance & Banking'],
            ['subject_code' => '2501', 'name' => 'Accounting'],
            ['subject_code' => '2601', 'name' => 'Management'],
            ['subject_code' => '2701', 'name' => 'Physics'],
            ['subject_code' => '2801', 'name' => 'Chemistry'],
            ['subject_code' => '2901', 'name' => 'Biochemistry'],
            ['subject_code' => '3001', 'name' => 'Botany'],
            ['subject_code' => '3101', 'name' => 'Zoology'],
            ['subject_code' => '3201', 'name' => 'Geography and Environment'],
            ['subject_code' => '3301', 'name' => 'Soil Science'],
            ['subject_code' => '3401', 'name' => 'Psychology'],
            ['subject_code' => '3501', 'name' => 'Home Economics'],
            ['subject_code' => '3601', 'name' => 'Statistics'],
            ['subject_code' => '3701', 'name' => 'Mathematics'],
            ['subject_code' => '3801', 'name' => 'Library & Information Science'],
            ['subject_code' => '3901', 'name' => 'Anthropology'],
            ['subject_code' => '4001', 'name' => 'Public Administration'],
            ['subject_code' => '4101', 'name' => 'Computer Science and Engineering (CSE)'],
            ['subject_code' => '4201', 'name' => 'Music'],
            ['subject_code' => '4401', 'name' => 'Environmental Science'],
            ['subject_code' => '5001', 'name' => 'Business Administration (BBA)'],
            ['subject_code' => '5101', 'name' => 'Tourism And Hospitality Management'],
            ['subject_code' => '5201', 'name' => 'Education (B.Ed)'],
            ['subject_code' => '5301', 'name' => 'Fine Arts (BFA)'],
            ['subject_code' => '5401', 'name' => 'Law (LLB)'],
            ['subject_code' => '6001', 'name' => 'ECE'],
            ['subject_code' => '6002', 'name' => 'FDT'],
            ['subject_code' => '6003', 'name' => 'KMT'],
            ['subject_code' => '6004', 'name' => 'TMS'],
            ['subject_code' => '6005', 'name' => 'AMT'],
        ];

        $now = Carbon::now();
        foreach ($subjects as &$subject) {
            $subject['created_at'] = $now;
            $subject['updated_at'] = $now;
        }

        DB::table('subjects')->insert($subjects);
    }
}

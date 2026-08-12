<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // অনার্স কোর্স (Honours)
            ['name' => 'Bachelor of Arts (B.A.) - Honours', 'category' => 'Honours'],
            ['name' => 'Bachelor of Social Science (B.S.S.) - Honours', 'category' => 'Honours'],
            ['name' => 'Bachelor of Science (B.Sc.) - Honours', 'category' => 'Honours'],
            ['name' => 'Bachelor of Business Administration (B.B.A.) - Honours', 'category' => 'Honours'],

            // ডিগ্রী পাস কোর্স (Degree Pass)
            ['name' => 'Bachelor of Arts (B.A.) - Pass', 'category' => 'Degree Pass'],
            ['name' => 'Bachelor of Social Science (B.S.S.) - Pass', 'category' => 'Degree Pass'],
            ['name' => 'Bachelor of Science (B.Sc.) - Pass', 'category' => 'Degree Pass'],
            ['name' => 'Bachelor of Business Studies (B.B.S.) - Pass', 'category' => 'Degree Pass'],

            // মাস্টার্স কোর্স (Masters)
            ['name' => 'Master of Arts (M.A.)', 'category' => 'Masters'],
            ['name' => 'Master of Social Science (M.S.S.)', 'category' => 'Masters'],
            ['name' => 'Master of Science (M.Sc.)', 'category' => 'Masters'],
            ['name' => 'Master of Business Studies (M.B.S.)', 'category' => 'Masters'],

            // প্রফেশনাল কোর্স (Professional)
            ['name' => 'Computer Science and Engineering (CSE)', 'category' => 'Professional'],
            ['name' => 'Bachelor of Business Administration (BBA) - Professional', 'category' => 'Professional'],
            ['name' => 'Bachelor of Education (B.Ed)', 'category' => 'Professional'],
            ['name' => 'Bachelor of Physical Education (BPEd)', 'category' => 'Professional'],
            ['name' => 'Bachelor of Laws (LLB)', 'category' => 'Professional'],
            ['name' => 'Tourism and Hospitality Management (THM)', 'category' => 'Professional'],
            ['name' => 'Electronics and Communication Engineering (ECE)', 'category' => 'Professional'],
            ['name' => 'Fashion Design and Technology (FDT)', 'category' => 'Professional'],
            ['name' => 'Knitwear Manufacture & Technology (KMT)', 'category' => 'Professional'],
            ['name' => 'Apparel Manufacture & Technology (AMT)', 'category' => 'Professional'],
        ];

        $now = Carbon::now();
        foreach ($courses as &$course) {
            $course['created_at'] = $now;
            $course['updated_at'] = $now;
        }

        DB::table('courses')->insert($courses);
    }
}

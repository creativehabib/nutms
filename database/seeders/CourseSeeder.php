<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            ['name' => 'BA', 'level' => 'degree'],
            ['name' => 'BSS', 'level' => 'degree'],
            ['name' => 'BSc', 'level' => 'degree'],
            ['name' => 'BBS', 'level' => 'degree'],
            ['name' => 'Computer Science and Engineering (CSE)', 'level' => 'professional'],
            ['name' => 'Bachelor of Business Administration (BBA)', 'level' => 'professional'],
            ['name' => 'Bachelor of Education (B.Ed)', 'level' => 'professional'],
            ['name' => 'Bachelor of Physical Education (BPEd)', 'level' => 'professional'],
            ['name' => 'Bachelor of Laws (LLB)', 'level' => 'professional'],
            ['name' => 'Tourism and Hospitality Management (THM)', 'level' => 'professional'],
            ['name' => 'Electronics and Communication Engineering (ECE)', 'level' => 'professional'],
            ['name' => 'Fashion Design and Technology (FDT)', 'level' => 'professional'],
            ['name' => 'Knitwear Manufacture & Technology (KMT)', 'level' => 'professional'],
            ['name' => 'Apparel Manufacture & Technology (AMT)', 'level' => 'professional'],
        ];

        collect($courses)->each(fn (array $course) => Course::query()->updateOrCreate(
            ['level' => $course['level'], 'name' => $course['name']],
            ['is_active' => true],
        ));
    }
}

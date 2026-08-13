<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            ['name' => 'Professor'],
            ['name' => 'Associate Professor'],
            ['name' => 'Assistant Professor'],
            ['name' => 'Lecturer'],
            ['name' => 'Demonstrator'],
        ];

        collect($courses)->each(fn (array $course) => Course::query()->updateOrCreate(
            ['is_active' => true],
        ));
    }
}

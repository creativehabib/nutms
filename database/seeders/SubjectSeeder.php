<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'Accounting'],
            ['name' => 'Amt'],
            ['name' => 'Anthropology'],
            ['name' => 'Arabic'],
            ['name' => 'B.Ed'],
            ['name' => 'Bangla'],
            ['name' => 'Bba'],
            ['name' => 'Bfa'],
            ['name' => 'Bio- Chemistry'],
            ['name' => 'Botany'],
            ['name' => 'Chemistry'],
            ['name' => 'Cse'],
            ['name' => 'Ece'],
            ['name' => 'Economics'],
            ['name' => 'Education'],
            ['name' => 'English'],
            ['name' => 'Environmean Tal Science'],
            ['name' => 'Fdt'],
            ['name' => 'Finance & Banking'],
            ['name' => 'Geography And'],
            ['name' => 'Geography And Environment'],
            ['name' => 'History'],
            ['name' => 'Home Economics'],
            ['name' => 'Islamic History &'],
            ['name' => 'Islamic History & Culture'],
            ['name' => 'Islamic Studies'],
            ['name' => 'Kmt'],
            ['name' => 'Library & Information Science'],
            ['name' => 'Llb'],
            ['name' => 'M.Ed'],
            ['name' => 'Management'],
            ['name' => 'Marketing'],
            ['name' => 'Mathematics'],
            ['name' => 'Music'],
            ['name' => 'Pali'],
            ['name' => 'Philosophy'],
            ['name' => 'Physics'],
            ['name' => 'Political Science'],
            ['name' => 'Psychology'],
            ['name' => 'Public Administrati On'],
            ['name' => 'Sanskrit'],
            ['name' => 'Social Work'],
            ['name' => 'Sociology'],
            ['name' => 'Soil Science'],
            ['name' => 'Statistics'],
            ['name' => 'Tms'],
            ['name' => 'Tourism And Hospitality Management'],
            ['name' => 'Zoology'],
        ];

        $now = Carbon::now();
        foreach ($subjects as &$subject) {
            $subject['created_at'] = $now;
            $subject['updated_at'] = $now;
        }

        DB::table('subjects')->insert($subjects);
    }
}

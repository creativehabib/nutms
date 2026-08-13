<?php

namespace Database\Seeders;

use App\Models\TeacherLevel;
use Illuminate\Database\Seeder;

class TeacherLevelSeeder extends Seeder
{
    public function run(): void
    {
        collect(['Honours', 'Degree'])->each(fn (string $name) => TeacherLevel::query()->updateOrCreate(
            ['name' => $name],
            ['is_active' => true],
        ));
    }
}

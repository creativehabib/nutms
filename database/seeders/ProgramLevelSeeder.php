<?php

namespace Database\Seeders;

use App\Models\ProgramLevel;
use Illuminate\Database\Seeder;

class ProgramLevelSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'Degree', 'slug' => 'degree', 'sort_order' => 10],
            ['name' => 'Honours', 'slug' => 'honours', 'sort_order' => 20],
            ['name' => 'Masters', 'slug' => 'masters', 'sort_order' => 30],
            ['name' => 'Professional', 'slug' => 'professional', 'sort_order' => 40],
            ['name' => 'Other', 'slug' => 'other', 'sort_order' => 50],
        ])->each(fn (array $level) => ProgramLevel::query()->updateOrCreate(
            ['slug' => $level['slug']],
            $level + ['is_active' => true],
        ));
    }
}

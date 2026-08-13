<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['name' => 'Professor'],
            ['name' => 'Associate Professor'],
            ['name' => 'Assistant Professor'],
            ['name' => 'Lecturer'],
            ['name' => 'Demonstrator'],
        ];

        collect($designations)->each(fn (array $designation) => Designation::query()->updateOrCreate(
            ['name' => $designation['name']],
            ['is_active' => true],
        ));
    }
}

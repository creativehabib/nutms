<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Training> */
class TrainingFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 day', '+30 days');

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify('+4 hours'),
            'registration_deadline' => (clone $startDate)->modify('-1 day'),
            'type' => fake()->randomElement(['Online', 'Offline', 'Hybrid']),
            'location_or_link' => fake()->url(),
            'instructor_name' => fake()->name(),
            'capacity' => 40,
            'has_certificate' => true,
            'status' => 'Upcoming',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'progress' => fake()->numberBetween(0, 100),
            'status' => fake()->randomElement(['active', 'completed', 'dropped']),
        ];
    }

    public function completed(): static
    {
        return $this->state(['progress' => 100, 'status' => 'completed']);
    }
}

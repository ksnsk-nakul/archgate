<?php

namespace Database\Factories;

use App\Models\LibraryContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LibraryContent>
 */
class LibraryContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'type' => fake()->randomElement(['article', 'video', 'pdf', 'audio']),
            'description' => fake()->paragraph(),
            'access_level' => fake()->randomElement(['free', 'basic', 'premium']),
        ];
    }
}

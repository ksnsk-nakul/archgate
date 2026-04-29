<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
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
            'content' => fake()->paragraphs(3, true),
            'type' => fake()->randomElement(['text', 'video', 'quiz']),
            'section_id' => Section::factory(),
            'order' => fake()->numberBetween(0, 20),
        ];
    }
}

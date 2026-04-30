<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'value' => fake()->randomFloat(2, 100, 100000),
            'stage_id' => PipelineStage::factory(),
            'contact_id' => null,
            'owner_id' => User::factory(),
        ];
    }
}

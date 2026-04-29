<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'tier' => fake()->randomElement(['free', 'basic', 'premium']),
            'price' => fake()->randomFloat(2, 0, 199),
            'features' => ['feature_1', 'feature_2'],
        ];
    }

    public function free(): static
    {
        return $this->state(['tier' => 'free', 'price' => 0]);
    }

    public function premium(): static
    {
        return $this->state(['tier' => 'premium', 'price' => 99.00]);
    }
}

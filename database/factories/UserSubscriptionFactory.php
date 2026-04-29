<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSubscription>
 */
class UserSubscriptionFactory extends Factory
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
            'plan_id' => SubscriptionPlan::factory(),
            'status' => 'active',
            'expires_at' => now()->addYear(),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function expired(): static
    {
        return $this->state(['status' => 'expired', 'expires_at' => now()->subDay()]);
    }
}

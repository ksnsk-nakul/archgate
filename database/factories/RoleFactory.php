<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected static array $defaults = ['Admin', 'Manager', 'Member'];

    protected static int $index = 0;

    public function definition(): array
    {
        $name = self::$defaults[self::$index++ % 3] ?? fake()->jobTitle();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(['name' => 'Admin', 'slug' => 'admin']);
    }

    public function manager(): static
    {
        return $this->state(['name' => 'Manager', 'slug' => 'manager']);
    }

    public function member(): static
    {
        return $this->state(['name' => 'Member', 'slug' => 'member']);
    }
}

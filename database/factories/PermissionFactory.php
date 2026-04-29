<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word().'.'.(fake()->randomElement(['view', 'create', 'update', 'delete']));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => null,
            'group' => null,
        ];
    }
}

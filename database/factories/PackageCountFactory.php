<?php

namespace Database\Factories;

use App\Models\PackageCount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackageCount>
 */
class PackageCountFactory extends Factory
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
            'quantity' => fake()->numberBetween(1, 200),
            'client_uuid' => fake()->uuid(),
            'counted_at' => now(),
        ];
    }
}

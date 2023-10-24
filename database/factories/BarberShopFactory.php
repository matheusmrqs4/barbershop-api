<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarberShop>
 */
class BarberShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'password' => bcrypt('password'),
            'address1' => fake()->streetAddress,
            'address2' => fake()->optional()->streetAddress,
            'address3' => fake()->optional()->streetAddress,
            'bio' => fake()->sentence(),
        ];
    }
}

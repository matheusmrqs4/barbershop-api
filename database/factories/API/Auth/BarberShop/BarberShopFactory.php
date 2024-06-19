<?php

namespace Database\Factories\API\Auth\BarberShop;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\API\Auth\BarberShop\BarberShop>
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
            'address1' => fake()->regexify('[A-Za-z0-9\s]{2,}'),
            'address2' => fake()->regexify('[A-Za-z0-9\s]{2,}'),
            'address3' => fake()->regexify('[A-Za-z0-9\s]{2,}'),
            'bio' => fake()->sentence(),
            'phone' => fake()->phoneNumber,
        ];
    }
}

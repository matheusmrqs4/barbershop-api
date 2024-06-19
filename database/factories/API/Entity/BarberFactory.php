<?php

namespace Database\Factories\API\Entity;

use App\Models\API\Auth\BarberShop\BarberShop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\API\Entity\Barber>
 */
class BarberFactory extends Factory
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
            'description' => fake()->sentence,
            'start_time' => fake()->time(),
            'end_time' => fake()->time(),
            'social_media' => fake()->sentence,
            'barber_shops_id' => function () {
                return BarberShop::factory()->create()->id;
            },
        ];
    }
}

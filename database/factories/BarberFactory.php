<?php

namespace Database\Factories;

use App\Models\BarberShop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barber>
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
            'barber_shops_id' => function () {
                return BarberShop::factory()->create()->id;
            },
        ];
    }
}

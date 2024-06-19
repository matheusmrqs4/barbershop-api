<?php

namespace Database\Factories\API\Entity;

use App\Models\API\Entity\Barber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\API\Entity\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->sentence,
            'duration' => fake()->numberBetween(15, 120),
            'price' => fake()->randomNumber(1, 50),
            'barbers_id' => Barber::factory(),
        ];
    }
}

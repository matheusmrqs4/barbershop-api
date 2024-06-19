<?php

namespace Database\Factories\API\Entity;

use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\API\Entity\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d H:i:s'),
            'services_id' => Service::factory()->create(),
            'barbers_id' => Barber::factory()->create(),
        ];
    }
}

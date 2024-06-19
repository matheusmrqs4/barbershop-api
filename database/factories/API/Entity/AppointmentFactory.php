<?php

namespace Database\Factories\API\Entity;

use App\Models\API\Auth\User\User;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Schedule;
use App\Models\API\Entity\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\API\Entity\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $schedule = Schedule::factory()->create();
        $barber = Barber::factory()->create();
        $service = Service::factory()->create();
        $user = User::factory()->create();

        return [
            'schedules_id' => $schedule->id,
            'users_id' => $user->id,
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
            'schedule_time' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }
}

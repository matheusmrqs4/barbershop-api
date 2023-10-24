<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
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

<?php

namespace Tests\Feature\Entity;

use App\Models\API\Auth\User\User;
use App\Models\API\Entity\Appointment;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Schedule;
use App\Models\API\Entity\Service;
use App\Notifications\AppointmentConfirmationNotification;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(User $user)
    {
        $token = JWTAuth::fromUser($user);

        return $token;
    }

    public function testAuthenticatedUserCanCreateAppointment()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $barber = Barber::factory()->create();

        $service = Service::factory()->create();

        $schedule = Schedule::factory()->create([
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        Notification::fake();

        $appointmentData = [
            'schedules_id' => $schedule->id,
            'users_id' => $user->id,
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
            'schedule_time' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/create-appointment', $appointmentData);

        $response->assertStatus(201);

        Notification::assertSentTo($user, AppointmentConfirmationNotification::class);
        Notification::assertSentTo($barber->barberShop, NewAppointmentNotification::class);
    }

    public function testUnauthenticatedUserCannotCreateAppointment()
    {
        $barber = Barber::factory()->create();

        $service = Service::factory()->create();

        $schedule = Schedule::factory()->create([
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        $appointmentData = [
            'schedules_id' => $schedule->id,
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
            'schedule_time' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('api/create-appointment', $appointmentData);

        $response->assertStatus(401);
    }

    public function testAuthenticatedUserCanViewAppointment()
    {
        $user = User::factory()->create();
        $token = $this->authenticateUser($user);

        $barber = Barber::factory()->create();
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create([
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        Notification::fake();

        $appointment = Appointment::factory()->create([
            'users_id' => $user->id,
            'schedules_id' => $schedule->id,
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
            'schedule_time' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/show-appointment/' . $appointment->id);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                    'data' => [
                        'message',
                        'appointment' => [
                            'id',
                            'users_id',
                            'schedules_id',
                            'services_id',
                            'barbers_id',
                            'schedule_time',
                            'created_at',
                            'updated_at',
                            'barbers',
                            'services',
                        ],
                    ],
                 ]);
    }

    public function testUnauthenticatedUserCannotViewAppointment()
    {
        $barber = Barber::factory()->create();
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create([
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        $appointment = Appointment::factory()->create([
            'users_id' => User::factory()->create()->id,
            'schedules_id' => $schedule->id,
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
            'schedule_time' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        $response = $this->getJson('api/show-appointment/' . $appointment->id);

        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Appointment;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(User $user)
    {
        $token = JWTAuth::fromUser($user);

        return $token;
    }

    public function testAuthUserCanCreateAppointment()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $schedule = Schedule::factory()->create();

        $data = [
        'schedules_id' => $schedule->id,
        ];

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/create-appointment', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('appointments', [
        'users_id' => $user->id,
        'schedules_id' => $schedule->id,
        ]);
    }

    public function testUnauthenticatedUserCannotCreateAppointment()
    {
        $schedule = Schedule::factory()->create();

        $data = [
        'schedules_id' => $schedule->id,
        ];

        $response = $this->postJson('api/create-appointment', $data);

        $response->assertStatus(401);
    }

    public function testAuthUserCanShowAppointment()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $appointment = Appointment::factory()->create(['users_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("api/show-appointment/{$appointment->id}");

        $response->assertStatus(201);
    }

    public function testAuthenticatedUserCannotViewOtherUserAppointment()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $appointmentUser1 = Appointment::factory()->create(['users_id' => $user1->id]);
        $appointmentUser2 = Appointment::factory()->create(['users_id' => $user2->id]);

        $token = $this->authenticateUser($user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("api/show-appointment/{$appointmentUser2->id}");

        $response->assertStatus(403);
    }
}

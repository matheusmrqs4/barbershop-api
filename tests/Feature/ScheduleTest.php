<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\BarberShop;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateBarberShop(BarberShop $barberShop)
    {
        $token = JWTAuth::fromUser($barberShop);

        return $token;
    }

    public function testBarberShopAuthCanCreateServices()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $schedule = Schedule::factory()->make()->toArray();

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/schedule', $schedule);

        $response->assertStatus(201);

        $this->assertDatabaseHas('schedules', [
            'services_id' => $schedule['services_id'],
            'barbers_id' => $schedule['barbers_id'],
        ]);
    }

    public function testBarberShopUnauthenticateCannotCreateBarbers()
    {
        $schedule = Schedule::factory()->create();

        $response = $this->postJson('api/schedule', [$schedule]);

        $response->assertStatus(401);
    }

    public function testBarberShopAuthCanListSchedules()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $schedule = Schedule::factory()->make();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/schedule', $schedule->toArray());

        $response->assertStatus(200);
    }

    public function testBarberShopAuthCanUpdateServices()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create();

        $scheduleData = [
        'schedule' => '2023-10-24 18:00:00',
        'services_id' => $service->id,
        'barbers_id' => $barber->id,
        ];

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/schedule/{$schedule->id}", $scheduleData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'schedule' => '2023-10-24 18:00:00',
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
        ]);
    }

    public function testBarberShopAuthCanDeleteSchedules()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $schedule = Schedule::factory()->create();

         $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
         ])->deleteJson("api/schedule/{$schedule->id}");

        $response->assertStatus(204);
    }
}

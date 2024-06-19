<?php

namespace Tests\Feature\Entity;

use App\Models\API\Auth\BarberShop\BarberShop;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateBarberShop(BarberShop $barberShop)
    {
        $token = JWTAuth::fromUser($barberShop);

        return $token;
    }

    public function testAuthenticatedBarberShopCanCreateSchedule()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $service = Service::factory()->for($barber)->create();

        $scheduleData = [
            'schedule' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/schedule', $scheduleData);

        $response->assertStatus(202)
            ->assertJson([
                'data' => [
                    'message' => 'Schedules creation process started successfully',
                    'barber' => [
                        'id' => $barber->id,
                        'name' => $barber->name,
                        'description' => $barber->description,
                        'social_media' => $barber->social_media,
                        'start_time' => $barber->start_time,
                        'end_time' => $barber->end_time,
                        'created_at' => $barber->created_at->format('Y-m-d\TH:i:s.u\Z'),
                        'updated_at' => $barber->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                        'barber_shops_id' => $barberShop->id,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('schedules', [
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
        ]);
    }


    public function testUnauthenticatedBarberShopCannotCreateSchedule()
    {
        $barber = Barber::factory()->for(BarberShop::factory())->create();

        $service = Service::factory()->for($barber)->create();

        $scheduleData = [
            'schedule' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'services_id' => $service->id,
            'barbers_id' => $barber->id,
        ];

        $response = $this->postJson('api/schedule', $scheduleData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function testAuthenticatedBarberShopCanUpdateSchedule()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $service = Service::factory()->for($barber)->create();

        $schedule = $barber->schedules()->create([
            'schedule' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        $scheduleData = [
            'schedule' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/schedule/{$schedule->id}", $scheduleData);

        $response->assertStatus(200)
            ->assertJsonPath('data.message', 'Schedule updated successfully');

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'schedule' => $scheduleData['schedule'],
        ]);
    }

    public function testUnauthenticatedBarberShopCannotUpdateSchedule()
    {
        $barberShop = BarberShop::factory()->create();

        $barber = Barber::factory()->for($barberShop)->create();

        $service = Service::factory()->for($barber)->create();

        $schedule = $barber->schedules()->create([
            'schedule' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        $scheduleData = [
            'schedule' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ];

        $response = $this->putJson("api/schedule/{$schedule->id}", $scheduleData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function testAuthenticatedBarberShopCanDeleteSchedule()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $service = Service::factory()->for($barber)->create();

        $schedule = $barber->schedules()->create([
            'schedule' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("api/schedule/{$schedule->id}");

        $response->assertStatus(204);
    }

    public function testUnauthenticatedBarberShopCannotDeleteSchedule()
    {
        $barber = Barber::factory()->for(BarberShop::factory())->create();

        $service = Service::factory()->for($barber)->create();

        $schedule = $barber->schedules()->create([
            'schedule' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'barbers_id' => $barber->id,
            'services_id' => $service->id,
        ]);

        $response = $this->deleteJson("api/schedule/{$schedule->id}");

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.'
                 ]);
    }
}

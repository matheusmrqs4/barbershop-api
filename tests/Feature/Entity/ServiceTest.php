<?php

namespace Tests\Feature\Entity;

use App\Models\API\Auth\BarberShop\BarberShop;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateBarberShop(BarberShop $barberShop)
    {
        $token = JWTAuth::fromUser($barberShop);

        return $token;
    }

    public function testAuthenticatedBarberShopCanCreateService()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $serviceData = Service::factory()->for($barber)->make()->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/service', $serviceData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'message' => 'Service created successfully',
                    'service' => [
                        'description' => $serviceData['description'],
                        'duration' => $serviceData['duration'],
                        'price' => $serviceData['price'],
                        'barbers_id' => $barber->id,
                    ]
                ]]);

        $this->assertDatabaseHas('services', [
            'description' => $serviceData['description'],
            'duration' => $serviceData['duration'],
            'price' => $serviceData['price'],
            'barbers_id' => $barber->id,
        ]);
    }

    public function testUnauthenticatedBarberShopCannotCreateService()
    {
        $barber = Barber::factory()->create();

        $service = Service::factory()->for($barber)->create();

        $response = $this->postJson('api/service', $service->toArray());

        $response->assertStatus(401)
                 ->assertJson([
                    'message' => 'Unauthenticated.'
                 ]);
    }

    public function testAuthenticatedBarberShopCanUpdateService()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for(BarberShop::factory())->create();

        $service = Service::factory()->for($barber)->create();

        $serviceData = [
            'description' => 'Service Description Update',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/service/{$service->id}", $serviceData);

        $response->assertStatus(200)
            ->assertJsonPath('data.message', 'Service updated successfully')
            ->assertJsonPath('data.service.description', 'Service Description Update');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'description' => 'Service Description Update',
        ]);
    }

    public function testUnauthenticatedBarberShopCannotUpdateService()
    {
        $service = Service::factory()->for(Barber::factory())->create();

        $serviceData = [
            'description' => 'updated description',
        ];

        $response = $this->putJson("api/service/{$service->id}", $serviceData);

        $response->assertStatus(401)
                 ->assertJson([
                    'message' => 'Unauthenticated.'
                 ]);
    }

    public function testAuthenticatedBarberShopCanDeleteService()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for(BarberShop::factory())->create();

        $service = Service::factory()->for($barber)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("api/service/{$service->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }

    public function testUnauthenticatedBarberShopCannotDeleteService()
    {
        $service = Service::factory()->for(Barber::factory())->create();

        $response = $this->deleteJson("api/barber/{$service->id}");

        $response->assertStatus(401)
                 ->assertJson([
                    'message' => 'Unauthenticated.'
                 ]);
    }
}

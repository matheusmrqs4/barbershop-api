<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Barber;
use App\Models\Service;
use App\Models\BarberShop;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
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

        $service = Service::factory()->make()->toArray();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/service', $service);

        $response->assertStatus(201);

        $this->assertDatabaseHas('services', $service);
    }

    public function testBarberShopUnauthenticateCannotCreateServices()
    {
        $service = Service::factory()->make();

        $response = $this->postJson('api/service', $service->toArray());

        $response->assertStatus(401);
    }

    public function testBarberShopAuthCanListServices()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $service = Service::factory()->make();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/service', $service->toArray());

        $response->assertStatus(200);
    }

    public function testBarberShopAuthCanUpdateServices()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();
        $service = Service::factory()->create();

        $serviceData = [
        'description' => 'Service Update',
        'duration' => 30,
        'price' => 20,
        'barbers_id' => $barber->id,
        ];

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/service/{$service->id}", $serviceData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'description' => 'Service Update',
        'duration' => 30,
        'price' => 20,
        'barbers_id' => $barber->id,
        ]);
    }

    public function testBarberShopAuthCanDeleteServices()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $service = Service::factory()->create();

         $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
         ])->deleteJson("api/service/{$service->id}");

        $response->assertStatus(204);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Barber;
use App\Models\BarberShop;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BarberTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateBarberShop(BarberShop $barberShop)
    {
        $token = JWTAuth::fromUser($barberShop);

        return $token;
    }

    public function testBarberShopAuthCanCreateBarbers()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for(BarberShop::factory())->make();
        $uniqueName = $barber->name;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/barber', $barber->toArray());

        $response->assertStatus(200);

        $this->assertDatabaseHas('barbers', ['name' => $uniqueName]);
    }

    public function testBarberShopUnauthenticateCannotCreateBarbers()
    {
        $barber = Barber::factory()->make();

        $response = $this->postJson('api/barber', $barber->toArray());

        $response->assertStatus(401);
    }

    public function testBarberShopAuthCanListBarbers()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for(BarberShop::factory())->make();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('api/barber', $barber->toArray());

        $response->assertStatus(200);
    }

    public function testBarberShopAuthCanUpdateBarbers()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $barberData = [
        'name' => 'Barber Update',
        'description' => 'Update Description',
        'start_time' => '10:00:00',
        'end_time' => '18:00:00',
        ];

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->patchJson("api/barber/{$barber->id}", $barberData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('barbers', [
        'id' => $barber->id,
        'name' => 'Barber Update',
        'description' => 'Update Description',
        'start_time' => '10:00:00',
        'end_time' => '18:00:00',
        ]);
    }

    public function testBarberShopAuthCanDeleteBarbers()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

         $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
         ])->deleteJson("api/barber/{$barber->id}");

        $response->assertStatus(204);
    }
}

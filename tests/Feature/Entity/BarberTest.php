<?php

namespace Tests\Feature\Entity;

use App\Models\API\Auth\BarberShop\BarberShop;
use App\Models\API\Entity\Barber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BarberTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateBarberShop(BarberShop $barberShop)
    {
        $token = JWTAuth::fromUser($barberShop);

        return $token;
    }

    public function testAuthenticatedBarberShopCanCreateBarber()
    {
        $barberShop = BarberShop::factory()->create();
        $token = $this->authenticateBarberShop($barberShop);
        $barber = Barber::factory()->for($barberShop)->make();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('api/barber', $barber->toArray());

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        'message' => 'Barber created successfully',
                        'barber' => $barber->toArray()
                    ]
                ]);

        $this->assertDatabaseHas('barbers', [
            'barber_shops_id' => $barberShop->id,
        ]);
    }

    public function testUnauthenticatedBarberShopCannotCreateBarber()
    {
        $barber = Barber::factory()->make();

        $response = $this->postJson('api/barber', $barber->toArray());

        $response->assertStatus(401)
                  ->assertJson([
                     'message' => 'Unauthenticated.'
                  ]);
    }

    public function testAuthenticatedBarberShopCanUpdateBarber()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $barberData = [
            'name' => 'Barber Name Update',
            'description' => 'Barber Description Update',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/barber/{$barber->id}", $barberData);

        $response->assertStatus(200)
                 ->assertJsonPath('data.message', 'Barber updated successfully')
                 ->assertJsonPath('data.barber.name', 'Barber Name Update')
                 ->assertJsonPath('data.barber.description', 'Barber Description Update');

        $this->assertDatabaseHas('barbers', [
            'id' => $barber->id,
            'name' => 'Barber Name Update',
            'description' => 'Barber Description Update',
        ]);
    }

    public function testUnauthenticatedBarberShopCannotUpdateBarber()
    {
        $barber = Barber::factory()->create();

        $barberData = [
            'name' => 'Barber Name Update',
            'description' => 'Barber Description Update',
        ];

        $response = $this->putJson("api/barber/{$barber->id}", $barberData);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.'
                 ]);
    }

    public function testAuthenticatedBarberShopCanDeleteBarber()
    {
        $barberShop = BarberShop::factory()->create();

        $token = $this->authenticateBarberShop($barberShop);

        $barber = Barber::factory()->for($barberShop)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("api/barber/{$barber->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('barbers', [
            'id' => $barber->id,
        ]);
    }

    public function testUnauthenticatedBarberShopCannotDeleteBarber()
    {
        $barber = Barber::factory()->create();

        $response = $this->deleteJson("api/barber/{$barber->id}");

        $response->assertStatus(401)
                 ->assertJson([
                    'message' => 'Unauthenticated.'
                 ]);
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\API\Auth\BarberShop\BarberShop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BarberShopTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateBarberShop(BarberShop $barberShop)
    {
        $token = JWTAuth::fromUser($barberShop);

        return $token;
    }

    public function testBarberShopCanCreateAccount()
    {
        $barberShopData = BarberShop::factory()->raw();

        $barberShopData['password'] = bcrypt($barberShopData['password']);

        $response = $this->postJson("api/barber-shop/register", $barberShopData);

        $response->assertStatus(201)
                 ->assertJson([
                    'data' => [
                        'message' => 'Successfully registered',
                        'barber_shop' => [
                            'name' => $barberShopData['name'],
                            'email' => $barberShopData['email'],
                            'address1' => $barberShopData['address1'],
                            'address2' => $barberShopData['address2'],
                            'address3' => $barberShopData['address3'],
                            'bio' => $barberShopData['bio'],
                            'phone' => $barberShopData['phone'],
                        ],
                        'token' => true,
                    ]
            ]);

        $this->assertDatabaseHas('barber_shops', [
            'name' => $barberShopData['name'],
            'email' => $barberShopData['email'],
            'address1' => $barberShopData['address1'],
            'address2' => $barberShopData['address2'],
            'address3' => $barberShopData['address3'],
            'bio' => $barberShopData['bio'],
            'phone' => $barberShopData['phone'],
        ]);
    }

    public function testBarberShopCanLogin()
    {
        $barberShop = BarberShop::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $credentials = [
            'email' => $barberShop->email,
            'password' => 'password123'
        ];

        $response = $this->postJson("api/barber-shop/login", $credentials);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'token' => true,
                     ]
                 ]);
    }

    public function testAuthenticatedBarberShopCanLogout()
    {
        $barberShop = BarberShop::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $token = $this->authenticateBarberShop($barberShop);

        $this->assertNotNull($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/barber-shop/logout");

        $response->assertStatus(200);
    }

    public function testAuthenticatedBarberShopCanUpdateProfile()
    {
        $barberShop = BarberShop::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $token = $this->authenticateBarberShop($barberShop);

        $this->assertNotNull($token);

        $update = [
            'bio' => fake()->sentence(),
            'address1' => fake()->streetAddress(),
            'address3' => fake()->streetAddress(),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/barber-shop/update-profile", $update);

        $response->assertStatus(200)
                 ->assertJson([
                    'data' => [
                        'message' => 'Profile updated successfully',
                        'barber_shop' => [
                            'bio' => $update['bio'],
                            'address1' => $update['address1'],
                            'address3' => $update['address3'],
                        ]
                    ]
                 ]);

        $this->assertDatabaseHas('barber_shops', [
            'id' => $barberShop->id,
            'bio' => $update['bio'],
            'address1' => $update['address1'],
            'address3' => $update['address3'],
        ]);
    }

    public function testUnauthenticatedBarberShopCannotUpdateProfile()
    {
        $update = [
            'bio' => fake()->sentence(),
            'address1' => fake()->streetAddress(),
            'address3' => fake()->streetAddress(),
        ];

        $response = $this->putJson("api/barber-shop/update-profile", $update);

        $response->assertStatus(401);
    }
}

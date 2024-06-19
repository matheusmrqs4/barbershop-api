<?php

namespace Tests\Feature\Auth;

use App\Models\API\Auth\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(User $user)
    {
        $token = JWTAuth::fromUser($user);

        return $token;
    }

    public function testUserCanCreateAccount()
    {
        $userData = User::factory()->raw();

        $userData['password'] = bcrypt($userData['password']);

        $response = $this->postJson("api/user/register", $userData);

        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'message' => 'Successfully registered',
                         'user' => [
                             'name' => $userData['name'],
                             'email' => $userData['email'],
                             'phone' => $userData['phone'],
                         ],
                         'token' => true
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
        ]);
    }

    public function testUserCanLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson("api/user/login", $credentials);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'message' => 'Login successful',
                         'token' => true,
                     ]
                 ]);
    }

    public function testAuthenticatedUserCanLogout()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $token = $this->authenticateUser($user);

        $this->assertNotNull($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/user/logout");

        $response->assertStatus(200);
    }

    public function testAuthenticatedUserCanUpdateProfile()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $token = $this->authenticateUser($user);

        $this->assertNotNull($token);

        $update = [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("api/user/update-profile", $update);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'message' => 'Profile updated successfully',
                         'user' => [
                             'name' => $update['name'],
                             'phone' => $update['phone'],
                         ]
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $update['name'],
            'phone' => $update['phone'],
        ]);
    }

    public function testUnauthenticatedUserCannotUpdateProfile()
    {
        $update = [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
        ];

        $response = $this->putJson("api/user/update-profile", $update);

        $response->assertStatus(401);
    }
}

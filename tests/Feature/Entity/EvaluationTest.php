<?php

namespace Tests\Feature\Entity;

use App\Models\API\Auth\User\User;
use App\Models\API\Entity\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EvaluationTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(User $user)
    {
        $token = JWTAuth::fromUser($user);
        return $token;
    }

    public function testAuthenticatedUserCanSubmitEvaluation()
    {
        $user = User::factory()->create();

        $token = $this->authenticateUser($user);

        $appointment = Appointment::factory()->create(['users_id' => $user->id]);

        $evaluationData = [
            'appointments_id' => $appointment->id,
            'comment' => 'test comment',
            'grade' => 2,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/appointment/{$appointment->id}/evaluate", $evaluationData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'message',
                    'event' => [
                        'id',
                        'comment',
                        'grade',
                        'appointments_id',
                        'users_id',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }

    public function testUnauthenticatedUserCannotSubmitEvaluation()
    {
        $user = User::factory()->create();

        $appointment = Appointment::factory()->create(['users_id' => $user->id]);

        $evaluationData = [
            'appointments_id' => $appointment->id,
            'comment' => 'test comment',
            'grade' => 2,
        ];

        $response = $this->postJson("api/appointment/{$appointment->id}/evaluate", $evaluationData);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.'
                 ]);
    }
}

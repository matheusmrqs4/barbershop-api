<?php

namespace Database\Factories\API\Entity;

use App\Models\API\Auth\User\User;
use App\Models\API\Entity\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\API\Entity\Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment' => fake()->sentence,
            'grade' => fake()->numberBetween(1, 5),
            'appointments_id' => Appointment::factory(),
            'users_id' => User::factory(),
        ];
    }
}

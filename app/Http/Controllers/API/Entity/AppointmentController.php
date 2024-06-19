<?php

namespace App\Http\Controllers\API\Entity;

use App\Http\Controllers\Controller;
use App\Models\API\Entity\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * @OA\Post(
     *     path="/api/create-appointment",
     *     summary="Create an appointment",
     *     tags={"Appointment"},
     *     operationId="createAppointment",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="schedules_id",
     *                     type="integer",
     *                     description="ID of the schedule"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="appointment", type="object",
     *                     @OA\Property(property="status", type="boolean"),
     *                     @OA\Property(property="message", type="string"),
     *                     @OA\Property(property="appointment", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="users_id", type="integer"),
     *                         @OA\Property(property="schedules_id", type="integer"),
     *                         @OA\Property(property="services_id", type="integer"),
     *                         @OA\Property(property="barbers_id", type="integer"),
     *                         @OA\Property(property="schedule_time", type="string", format="date-time"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     ),
     *                     @OA\Property(property="barber", type="object"),
     *                     @OA\Property(property="service", type="object"),
     *                     @OA\Property(property="schedule", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Schedule unavailable or error finding associated entities"
     *     )
     * )
     */
    public function createAppointment(Request $request)
    {
        $appointment = $this->appointmentService->createAppointment($request->schedules_id);

        if (!$appointment['status']) {
            return response()
                ->json([
                    'message' => $appointment['message']
                ], 500);
        }

        return response()
            ->json([
                'data' => [
                    'appointment' => $appointment
                ]], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/show-appointment/{appointment}",
     *     summary="Get details of an appointment",
     *     tags={"Appointment"},
     *     operationId="showAppointment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="appointment",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="appointment", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="users_id", type="integer"),
     *                     @OA\Property(property="schedules_id", type="integer"),
     *                     @OA\Property(property="services_id", type="integer"),
     *                     @OA\Property(property="barbers_id", type="integer"),
     *                     @OA\Property(property="schedule_time", type="string", format="date-time"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="barbers", type="object"),
     *                     @OA\Property(property="services", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error fetching appointment details"
     *     )
     * )
     */
    public function showAppointment(Appointment $appointment)
    {
        try {
            $user = Auth::user();

            if ($user->id !== $appointment->users_id) {
                abort(403, "Unauthorized");
            }

            $appointment->barbers;
            $appointment->services;

            return response()
                ->json([
                    'data' => [
                        'message' => 'Appointment details',
                        'appointment' => $appointment,
                    ]
                ], 201);
        } catch (\Exception $e) {
            Log::error('Error fetching appointment details: ' . $e->getMessage());
            return response()
                ->json([
                    'message' => 'Error fetching appointment details'
                ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\API\Entity;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSchedule;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Schedule;
use App\Models\API\Entity\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class ScheduleController extends Controller
{
    private $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @OA\Get(
     *     path="/api/schedule",
     *     summary="Get all schedules",
     *     tags={"Schedules"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="schedule",
     *                     type="object",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting schedules"
     *     )
     * )
     */
    public function index()
    {
        try {
            $schedule = Schedule::with('services', 'barbers')->get();

            return response()
                ->json([
                    'data' => [
                        'schedule' => $schedule,
                    ]
                ]);
        } catch (\Exception $exception) {
            Log::error('Error getting schedules: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting schedules'
                ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/schedule",
     *     summary="Create schedules",
     *     tags={"Schedules"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"barbers_id", "services_id"},
     *             @OA\Property(
     *                 property="barbers_id",
     *                 type="integer",
     *                 description="ID of the barber for whom to create schedules"
     *             ),
     *             @OA\Property(
     *                 property="services_id",
     *                 type="integer",
     *                 description="ID of the service to assign to schedules"
     *             ),
     *             @OA\Property(
     *                 property="days",
     *                 type="integer",
     *                 description="Number of days to add schedules for (default: 3)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Schedules creation process started successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Schedules creation process started successfully"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Barber ID or Services ID is required"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Barber not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Barber not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error create schedules"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $requestData = $request->all();
            $barberID = $request->barbers_id;
            $barber = Barber::find($barberID);
            $daysToAdd = $request->input('days', 3);

            if (!$barber) {
                return response()->json([
                    'message' => 'Barber not found'
                ], 404);
            }

            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            ProcessSchedule::dispatch($barber, $daysToAdd);

            return response()
                ->json([
                    'data' => [
                        'message' => 'Schedules creation process started successfully',
                        'barber' => $barber
                    ]
                ], 202);
        } catch (\Exception $exception) {
            Log::error('Error create schedules: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error create schedules'
                ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/schedule/{schedule}",
     *     summary="Get schedule by ID",
     *     tags={"Schedules"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         required=true,
     *         description="ID of the schedule",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="schedule",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Schedule not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting schedule"
     *     )
     * )
     */
    public function show(Schedule $schedule)
    {
        try {
            $schedule->services;
            $schedule->barbers;

            return response()
                ->json([
                    'data' => [
                        'schedule' => $schedule,
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error getting schedule: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting schedule'
                ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/schedule/{schedule}",
     *     summary="Update schedule",
     *     tags={"Schedules"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         required=true,
     *         description="ID of the schedule",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="services_id",
     *                 type="integer",
     *                 description="ID of the service"
     *             ),
     *             @OA\Property(
     *                 property="barbers_id",
     *                 type="integer",
     *                 description="ID of the barber"
     *             ),
     *             @OA\Property(
     *                 property="schedule",
     *                 type="string",
     *                 format="date-time",
     *                 description="Schedule date and time"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Schedule updated successfully"
     *                 ),
     *                 @OA\Property(
     *                     property="schedule",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "services_id": {"The services id field is required."},
     *                     "barbers_id": {"The barbers id field is required."}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Schedule not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error update schedule"
     *     )
     * )
     */
    public function update(Request $request, Schedule $schedule)
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            $schedule->update($request->all());

            return response()
                ->json([
                    'data' => [
                        'message' => 'Schedule updated successfully',
                        'schedule' => $schedule,
                    ]
                ]);
        } catch (\Exception $exception) {
            Log::error('Error update schedule: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error update schedule'
                ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/schedule/{schedule}",
     *     summary="Delete schedule",
     *     tags={"Schedules"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         required=true,
     *         description="ID of the schedule",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Schedule deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Schedule not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error delete schedule"
     *     )
     * )
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            $this->schedule = $schedule->delete();

            return response()
                ->json([
                    'data' => [
                        'message' => 'Schedule deleted successfully'
                    ]
                ], 204);
        } catch (\Exception $exception) {
            Log::error('Error delete schedule: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error delete schedule'
                ], 500);
        }
    }
}

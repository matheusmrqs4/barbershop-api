<?php

namespace App\Http\Controllers\API\Entity;

use App\Http\Controllers\Controller;
use App\Models\API\Entity\Appointment;
use App\Models\API\Entity\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('evaluation.validate')->only('store');
    }

    /**
     * @OA\Post(
     *     path="/api/appointment/{appointment}/evaluate",
     *     summary="Submit a new evaluation",
     *     tags={"Evaluation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"appointments_id", "comment", "grade"},
     *             @OA\Property(
     *                 property="appointments_id",
     *                 type="integer",
     *                 description="ID of the appointment",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 description="Comment for the evaluation",
     *                 example="Great service!"
     *             ),
     *             @OA\Property(
     *                 property="grade",
     *                 type="integer",
     *                 description="Grade given for the appointment",
     *                 example="5"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evaluation submitted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Evaluation submitted successfully"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid data provided"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not authorized to evaluate this appointment"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Appointment not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error submitting evaluation"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $appointmentID = $request->appointments_id;

            $appointment = Appointment::find($appointmentID);

            if (!$appointment) {
                return response()
                    ->json([
                        'message' => 'Appointment not found'
                    ], 404);
            }

            if ($user->id !== $appointment->users_id) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

            $evaluation = new Evaluation([
                'comment' => $request->comment,
                'grade' => $request->grade,
                'appointments_id' => $appointment->id,
                'users_id' => $user->id
            ]);

            $evaluation->save();

            return response()
                ->json([
                    'data' => [
                        'message' => 'Evaluation submitted successfully',
                        'event' => $evaluation
                    ]
                ]);
        } catch (\Exception $exception) {
            Log::error('Error submitting evaluation: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error submitting evaluation'
                ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/evaluation/{evaluation}",
     *     summary="Get details of a specific evaluation",
     *     tags={"Evaluation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="evaluation",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Evaluation details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Evaluation not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error fetching evaluation"
     *     )
     * )
     */
    public function show(Evaluation $evaluation)
    {
        try {
            return response()->json([
                'data' => [
                    'evaluation' => $evaluation
                ]
            ], 200);
        } catch (\Exception $exception) {
            Log::error('Error fetching evaluation: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error fetching evaluation'
                ], 500);
        }
    }
}

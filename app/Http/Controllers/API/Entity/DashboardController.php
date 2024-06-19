<?php

namespace App\Http\Controllers\API\Entity;

use App\Http\Controllers\Controller;
use App\Models\API\Entity\Appointment;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Schedule;
use App\Models\API\Entity\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     summary="Get dashboard data",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="barbers",
     *                     type="object",
     *                 ),
     *                 @OA\Property(
     *                     property="services",
     *                     type="object",
     *                 ),
     *                 @OA\Property(
     *                     property="schedules",
     *                     type="object",
     *                 ),
     *                 @OA\Property(
     *                     property="appointments",
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
     *         description="Error getting dashboard"
     *     )
     * )
     */
    public function index()
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if ($barberShop) {
                $barber = Barber::where('barber_shops_id', $barberShop->id)->get();

                $service = Service::with('barber')->whereHas('barber', function ($query) use ($barberShop) {
                    $query->where('barber_shops_id', $barberShop->id);
                })->get();

                $schedule = Schedule::whereIn('barbers_id', $barber->pluck('id'))->get();

                $appointment = Appointment::with('barbers', 'services', 'schedules', 'users')
                    ->whereIn('barbers_id', $barber->pluck('id'))->get();

                return response()
                    ->json([
                        'data' => [
                            'barber-shop' => $barberShop,
                            'barbers' => $barber,
                            'services' => $service,
                            'schedules' => $schedule,
                            'appointments' => $appointment,
                        ]
                    ]);
            }
        } catch (\Exception $exception) {
            Log::error('Error getting dashboard: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting dashboard'
                ], 500);
        }
    }
}

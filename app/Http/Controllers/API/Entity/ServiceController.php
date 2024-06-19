<?php

namespace App\Http\Controllers\API\Entity;

use App\Http\Controllers\Controller;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class ServiceController extends Controller
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->middleware('services.validate')->only('store');
    }

    /**
     * @OA\Get(
     *     path="/api/service",
     *     summary="Get all services",
     *     tags={"Service"},
     *     @OA\Response(
     *         response=200,
     *         description="Returns all services",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting services"
     *     )
     * )
     */
    public function index()
    {
        try {
            $services = Service::get();

            return response()
                ->json([
                    'data' => [
                        'services' => $services
                    ]
                ]);
        } catch (\Exception $exception) {
            Log::error('Error getting services: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting services'
                ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/service",
     *     summary="Create a new service",
     *     tags={"Service"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Service created successfully"
     *                 ),
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
     *         description="Forbidden - Barber shop not authorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Barber not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating service"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $requestData = $request->all();
            $barberID = $requestData['barbers_id'];

            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            $barber = Barber::find($barberID);

            if (!$barber) {
                return response()
                    ->json([
                        'message' => 'Barber not found'
                    ], 404);
            }

            $service = new Service($requestData);
            $barber->services()->save($service);


            return response()
                ->json([
                    'data' => [
                        'message' => 'Service created successfully',
                        'service' => $service
                    ]
                ], 201);
        } catch (\Exception $exception) {
            Log::error('Error create services: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error create services'
                ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/service/{service}",
     *     summary="Get details of a specific service",
     *     tags={"Service"},
     *     @OA\Parameter(
     *         name="service",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service details",
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
     *         description="Not Found - Service not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting service"
     *     )
     * )
     */
    public function show(Service $service)
    {
        try {
            $service->barber;

            return response()
                ->json([
                    'data' => [
                        'service' => $service,
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error getting service: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting service'
                ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/service/{service}",
     *     summary="Update a specific service",
     *     tags={"Service"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="service",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Service updated successfully"
     *                 ),
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
     *         description="Forbidden - Barber shop not authorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Service not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error updating service"
     *     )
     * )
     */
    public function update(Request $request, Service $service)
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            $service->update($request->all());

            return response()
                ->json([
                    'data' => [
                        'message' => 'Service updated successfully',
                        'service' => $service
                    ]
                ]);
        } catch (\Exception $exception) {
            Log::error('Error update service: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error update service'
                ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/service/{service}",
     *     summary="Delete a specific service",
     *     tags={"Service"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="service",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Service deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Barber shop not authorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Service not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting service"
     *     )
     * )
     */
    public function destroy(Service $service)
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            $this->service = $service->delete();

            return response()
                ->json([
                    'message' => 'Service deleted successfully'
                ], 204);
        } catch (\Exception $exception) {
            Log::error('Error delete service: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error delete service'
                ], 500);
        }
    }
}

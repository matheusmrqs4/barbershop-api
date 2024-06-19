<?php

namespace App\Http\Controllers\API\Entity;

use App\Http\Controllers\Controller;
use App\Models\API\Entity\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class BarberController extends Controller
{
    private $barber;

    public function __construct(Barber $barber)
    {
        $this->barber = $barber;
        $this->middleware('barbers.validate')->only('store');
    }

    /**
     * @OA\Get(
     *     path="/api/barber",
     *     summary="Get all barbers",
     *     tags={"Barber"},
     *     @OA\Response(
     *         response=200,
     *         description="Returns all posts with related user and comments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="barbers",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer"
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string"
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
    public function index()
    {
        try {
            $barber = $this->barber->get();

            return response()
                ->json([
                    'data' => [
                        'barbers' => $barber,
                    ]
                ]);
        } catch (\Exception $exception) {
            Log::error('Error getting barbers: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting barbers'
                ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/barber",
     *     summary="Create a new Barber",
     *     tags={"Barber"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "image"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the barber",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file of the barber"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Barber created successfully",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating barber"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $requestData = $request->all();
            $barberShop = Auth::guard('barber_shop')->user();

            if (!$barberShop) {
                abort(403, "Unauthorized");
            }

            $this->barber = $barberShop->barber()->create($requestData);

            return response()
                ->json([
                    'data' => [
                        'message' => 'Barber created successfully',
                        'barber' => $this->barber
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error create barber: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error create barber'
                ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/barber/{barber}",
     *     summary="Get a specific barber",
     *     tags={"Barber"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="barber",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Barber details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting barber"
     *     )
     * )
     */
    public function show(Barber $barber)
    {
        try {
            $barber->load('services', 'evaluations');

            return response()
                ->json([
                    'data' => [
                        'barber' => $barber,
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error getting barber: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error getting barber'
                ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/barber/{barber}",
     *     summary="Update a specific barber",
     *     tags={"Barber"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="barber",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Barber updated successfully",
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=501,
     *         description="Error updating barber"
     *     )
     * )
     */
    public function update(Request $request, Barber $barber)
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if ($barber->barber_shops_id !== $barberShop->id) {
                abort(403, "Unauthorized");
            }

            $barber->update($request->all());

            return response()
                ->json([
                    'data' => [
                        'message' => 'Barber updated successfully',
                        'barber' => $barber
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error update barber: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error update barber'
                ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/barber/{barber}",
     *     summary="Delete a specific barber",
     *     tags={"Barber"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="barber",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Barber deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting barber"
     *     )
     * )
     */
    public function destroy(Barber $barber)
    {
        try {
            $barberShop = Auth::guard('barber_shop')->user();

            if ($barber->barber_shops_id !== $barberShop->id) {
                abort(403, "Unauthorized");
            }

            $this->barber = $barber->delete();

            return response()
                ->json([
                    'message' => 'Barber deleted successfully'
                ], 204);
        } catch (\Exception $exception) {
            Log::error('Error delete barber: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error delete barber'
                ], 500);
        }
    }
}

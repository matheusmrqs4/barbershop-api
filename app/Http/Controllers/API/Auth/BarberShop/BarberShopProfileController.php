<?php

namespace App\Http\Controllers\API\Auth\BarberShop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class BarberShopProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/barber-shop/profile",
     *     summary="Get BarberShop Profile",
     *     tags={"BarberShop Profile"},
     *     operationId="profile",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="barber_shop", type="object",
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
     *         description="Error fetching profile"
     *     )
     * )
     */
    public function profile()
    {
        try {
            $barberShop = Auth::user();
            if (!$barberShop) {
                return response()
                    ->json([
                        'message' => 'Unauthorized'
                    ], 401);
            }

            $barberShop->load('barber');

            return response()
                ->json([
                    'data' => [
                        'barber_shop' => $barberShop,
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error fetching profile: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error fetching profile'
                ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/barber-shop/update-profile",
     *     summary="Update BarberShop Profile",
     *     tags={"BarberShop Profile"},
     *     operationId="updateProfile",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="BarberShop name"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="BarberShop phone"
     *                 ),
     *                 @OA\Property(
     *                     property="address1",
     *                     type="string",
     *                     description="BarberShop Address line 1"
     *                 ),
     *                 @OA\Property(
     *                     property="address2",
     *                     type="string",
     *                     description="BarberShop Address line 2"
     *                 ),
     *                 @OA\Property(
     *                     property="address3",
     *                     type="string",
     *                     description="BarberShop Address line 3"
     *                 ),
     *                 @OA\Property(
     *                     property="bio",
     *                     type="string",
     *                     description="BarberShop biography"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="barber_shop", type="object",
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
     *         description="Error updating profile"
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        try {
            $barberShop = Auth::user();
            if (!$barberShop) {
                return response()
                    ->json([
                        'message' => 'Unauthorized'
                    ], 401);
            }

            $barberShopData = $request->only('name', 'phone', 'address1', 'address2', 'address3', 'bio');
            $barberShop->update($barberShopData);

            return response()
                ->json([
                    'data' => [
                        'message' => 'Profile updated successfully',
                        'barber_shop' => $barberShop
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error updating profile: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error updating profile'
                ], 500);
        }
    }
}

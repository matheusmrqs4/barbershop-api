<?php

namespace App\Http\Controllers\API\Auth\BarberShop;

use App\Http\Controllers\Controller;
use App\Models\API\Auth\BarberShop\BarberShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Info(
 *     title="BarberShop API",
 *     description="Appointment Scheduling System for Barbershops.",
 *     version="1.0.0",
 *     contact={
 *         "url": "https://github.com/matheusmrqs4"
 *     },
 *     license={
 *         "name": "Apache 2.0",
 *         "url": "https://www.apache.org/licenses/LICENSE-2.0.html"
 *     }
 * )
 */

class BarberShopController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/barber-shop/register",
     *     summary="BarberShop Register",
     *     tags={"BarberShop Authentication"},
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="BarberShop name"
     *                  ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="BarberShop email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="BarberShop password"
     *                 ),
     *                 @OA\Property(
     *                     property="adress1",
     *                     type="string",
     *                     description="BarberShop Address line 1"
     *                 ),
     *                 @OA\Property(
     *                     property="adress2",
     *                     type="string",
     *                     description="BarberShop Address line 2"
     *                  ),
     *                 @OA\Property(
     *                     property="adress3",
     *                     type="string",
     *                     description="BarberShop Address line 3"
     *                  ),
     *                 @OA\Property(
     *                     property="bio",
     *                     type="string",
     *                     description="BarberShop Biography"
     *                  ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="BarberShop Phone"
     *                  ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function register(Request $request, BarberShop $barberShop)
    {
        $barberShopData = $request
            ->only('name', 'email', 'password', 'address1', 'address2', 'address3', 'bio', 'phone');
        $barberShopData['password'] = bcrypt($barberShopData['password']);

        try {
            $barberShop = $barberShop->create($barberShopData);
        } catch (\Exception $exception) {
            Log::error('Error creating new BarberShop: ' . $exception->getMessage());
            return response()->json([
                'message' => 'Error creating new BarberShop'
            ], 500);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::guard('barber_shop')->attempt($credentials);

        return response()->json([
            'data' => [
                'message' => 'Successfully registered',
                'barber_shop' => $barberShop,
                'token' => $token,
            ]
        ], 201);
    }


    /**
     * @OA\Post(
     *     path="/api/barber-shop/login",
     *     summary="BarberShop Login",
     *     tags={"BarberShop Authentication"},
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="BarberShop email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="BarberShop password"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred when trying to log in"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth('barber_shop')->attempt($credentials)) {
                return response()
                ->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (\Exception $exception) {
            Log::error('Error during login: ' . $exception->getMessage());
            return response()
            ->json([
                'message' => 'An error occurred when trying to log in'
            ], 500);
        }

        return response()
        ->json([
            'data' => [
                'message' => 'Login successful',
                'token' => $token
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/barber-shop/refresh",
     *     summary="Refresh BarberShop Token",
     *     tags={"BarberShop Authentication"},
     *     operationId="refresh",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred when trying to refresh token"
     *     )
     * )
     */
    public function refresh()
    {
        try {
            $token = auth('barber_shop')->refresh();
        } catch (\Exception $exception) {
            Log::error('Error refreshing token: ' . $exception->getMessage());
            return response()
            ->json([
                'message' => 'An error occurred when trying to refresh token'
            ], 500);
        }

        return response()
        ->json([
            'data' => [
                'message' => 'Token refreshed successfully',
                'token' => $token
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/barber-shop/logout",
     *     summary="Logout BarberShop",
     *     tags={"BarberShop Authentication"},
     *     operationId="logout",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred when trying to logout"
     *     )
     * )
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            Auth::guard('barber_shop')->logout();
        } catch (\Exception $exception) {
            Log::error('Error during logout: ' . $exception->getMessage());
            return response()
            ->json([
                'message' => 'An error occurred when trying to logout'
            ], 500);
        }

        return response()
        ->json([
            'message' => 'Logout successfully'
        ], 200);
    }
}

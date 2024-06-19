<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Http\Controllers\Controller;
use App\Models\API\Auth\User\User;
use App\Models\API\Entity\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/register",
     *     summary="User Register",
     *     tags={"User Authentication"},
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User name"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="User phone"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating new User"
     *     )
     * )
     */
    public function register(Request $request, User $user)
    {
        $userData = $request->only('name', 'email', 'password', 'phone');
        $userData['password'] = bcrypt($userData['password']);

        try {
            $user = $user->create($userData);
        } catch (\Exception $exception) {
            Log::error('Error creating new User: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error creating new User'
                ], 500);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::guard('api')->attempt($credentials);

        return response()
            ->json([
                'data' => [
                    'message' => 'Successfully registered',
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/user/login",
     *     summary="User Login",
     *     tags={"User Authentication"},
     *     operationId="loginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
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
            if (!$token = auth('api')->attempt($credentials)) {
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
     *     path="/api/user/refresh",
     *     summary="Refresh User Token",
     *     tags={"User Authentication"},
     *     operationId="refreshUserToken",
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
            $token = auth('api')->refresh();
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
     *     path="/api/user/logout",
     *     summary="User Logout",
     *     tags={"User Authentication"},
     *     operationId="logoutUser",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
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
            Auth::guard('api')->logout();
        } catch (\Exception $exception) {
            Log::error('Error during logout: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'An error occurred when trying to logout'
                ], 500);
        }

        return response()
            ->json([
                'message' => 'Logout successful'
            ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/appointments",
     *     summary="Get User Appointments",
     *     tags={"User Authentication"},
     *     operationId="getUserAppointments",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="appointments", type="array",
     *                     @OA\Items(type="object")
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
     *         description="An error occurred when trying to get appointments"
     *     )
     * )
     */
    public function userAppointments()
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()
                    ->json([
                        'message' => 'Unauthorized'
                    ], 401);
            }

            $appointments = Appointment::with('services', 'barbers')
                ->where('users_id', $user->id)
                ->get();

            return response()
                ->json([
                    'data' => [
                        'user' => $user,
                        'appointments' => $appointments
                    ]
                ], 200);
        } catch (\Exception $exception) {
            Log::error('Error fetching appointments: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'An error occurred when trying to get appointments'
                ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class UserProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     summary="Get User Profile",
     *     tags={"User Profile"},
     *     operationId="getUserProfile",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
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
            $user = auth()->user();
            if (!$user) {
                return response()
                    ->json([
                        'message' => 'Unauthorized'
                    ], 401);
            }

            return response()
                ->json(
                    $user,
                    200
                );
        } catch (\Exception $exception) {
            Log::error('Error fetching profile: ' . $exception->getMessage());
            return response()
                ->json([
                    'message' => 'Error fetching profile'
                ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/update-profile",
     *     summary="Update User Profile",
     *     tags={"User Profile"},
     *     operationId="updateUserProfile",
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
     *                     description="User name"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="User phone"
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
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="phone", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
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
            $user = Auth::user();
            if (!$user) {
                return response()
                    ->json([
                        'message' => 'Unauthorized'
                    ], 401);
            }

            $userData = $request->only('name', 'phone');
            $user->update($userData);

            return response()
                ->json([
                    'data' => [
                        'message' => 'Profile updated successfully',
                        'user' => $user
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

<?php

namespace App\Http\Controllers\Auth\User;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            abort(403, "Invalid Credentials");
        }

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Login Successfully',
                        'token' => $token
                    ]
                ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Refreshing',
                        'token' => $token
                    ]
                ]);
    }

    public function me()
    {
        return response()
                ->json(
                    auth()
                    ->user()
                );
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        Auth::guard('api')->logout();

        return response()
                ->json([
                    'msg' => 'Logout Successfully'
                ]);
    }

    public function userAppointments()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()
            ->json([
                'error' => 'User not authenticated'
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
    }
}

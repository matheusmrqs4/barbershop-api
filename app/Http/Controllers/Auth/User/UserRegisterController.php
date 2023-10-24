<?php

namespace App\Http\Controllers\Auth\User;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class UserRegisterController extends Controller
{
    public function register(Request $request, User $user)
    {
        $userData = $request->only('name', 'email', 'password');
        $userData['password'] = bcrypt($userData['password']);

        if (!$user = $user->create($userData)) {
            abort(500, "Error to Create new User");
        }

        $token = JWTAuth::attempt(['email' => $userData['email'], 'password' => $userData['password']]);

        return response()
                ->json([
                    'data' => [
                        'msg' => "Successfully",
                        'user' => $user,
                        'token' => $token
                    ]
                ], 200);
    }
}

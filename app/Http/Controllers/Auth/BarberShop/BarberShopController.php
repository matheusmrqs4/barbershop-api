<?php

namespace App\Http\Controllers\Auth\BarberShop;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BarberShopController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('barber_shop')->attempt($credentials)) {
            abort(403, "Invalid Credentials");
        }

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Login Successfully',
                        'token' => $token
                    ]
                ], 200);
    }

    public function me()
    {
        $barberShop = Auth::user();
        $barberShop->barber;

        return response()
                ->json([
                    'data' => [
                        'barber_shop' => $barberShop,
                    ]
                ], 200);
    }

    public function refresh()
    {
        $token = auth('barber_shop')->refresh();

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Refreshing',
                        'token' => $token
                    ]
                ], 200);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        Auth::guard('barber_shop')->logout();

        return response()
                ->json([
                    'msg' => 'Logout Successful'
                ], 200);
    }
}

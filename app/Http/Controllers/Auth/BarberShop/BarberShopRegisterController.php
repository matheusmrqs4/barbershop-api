<?php

namespace App\Http\Controllers\Auth\BarberShop;

use App\Models\BarberShop;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class BarberShopRegisterController extends Controller
{
    public function register(Request $request, BarberShop $barberShop)
    {
        $barberShopData = $request->only('name', 'email', 'password', 'address1', 'address2', 'address3', 'bio');
        $barberShopData['password'] = bcrypt($barberShopData['password']);

        if (!$barberShop = $barberShop->create($barberShopData)) {
            abort(500, "Error to Create new Barber Shop");
        }

        $token = JWTAuth::attempt(['email' => $barberShopData['email'], 'password' => $barberShopData['password']]);

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Successfully',
                        'barber-shop' => $barberShop,
                        'token' => $token
                    ]
                ]);
    }
}

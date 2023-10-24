<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarberController extends Controller
{
    private $barber;

    public function __construct(Barber $barber)
    {
        $this->barber = $barber;
        $this->middleware('barbers.validate')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barber = $this->barber->get();

        return response()
                ->json([
                    'data' => [
                        'barbers' => $barber,
                    ]
                ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->all();

        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $this->barber = $barberShop->barber()->create($requestData);

        return response()
            ->json($this->barber);
    }

    /**
     * Display the specified resource.
     */
    public function show(Barber $barber)
    {
        $barber->services;

        return response()
            ->json([
                'data' => [
                    'barber' => $barber,
                ]
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barber $barber)
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if ($barber->barber_shops_id !== $barberShop->id) {
            abort(403, "Unauthorized");
        }

        $barber->update($request->all());

        return $barber;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barber $barber)
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if ($barber->barber_shops_id !== $barberShop->id) {
            abort(403, "Unauthorized");
        }

        $this->barber = $barber->delete();

        return response()
            ->json([
                'data' => [
                    'msg' => 'Deleted Successfully'
                ]
            ], 204);
    }
}

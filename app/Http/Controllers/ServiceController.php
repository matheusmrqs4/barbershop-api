<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->middleware('services.validate')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::get();

        return response()
            ->json($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $barberID = $requestData['barbers_id'];

        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $barber = Barber::find($barberID);

        if (!$barber) {
            return response()
            ->json([
                'msg' => 'Barber not found'
            ], 404);
        }

        $service = new Service($requestData);
        $barber->services()->save($service);


        return response()
        ->json([
            'msg' => 'Service saved successfully',
            'data' => $service
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->barber;

        return response()
                ->json([
                    'data' => [
                      'service' => $service,
                    ]
                ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $service->update($request->all());

        return response()
                ->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $this->service = $service->delete();

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Deleted Successfully'
                    ]
                ], 204);
    }
}

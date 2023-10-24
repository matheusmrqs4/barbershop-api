<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Service;
use App\Models\Schedule;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if ($barberShop) {
            $barber = Barber::where('barber_shops_id', $barberShop->id)->get();

            $service = Service::with('barber')->whereHas('barber', function ($query) use ($barberShop) {
                $query->where('barber_shops_id', $barberShop->id);
            })->get();

            $schedule = Schedule::whereIn('barbers_id', $barber->pluck('id'))->get();

            $appointment = Appointment::with('barbers', 'services', 'schedules', 'users')
                                        ->whereIn('barbers_id', $barber->pluck('id'))->get();

            return response()
                    ->json([
                    'data' => [
                        'barber-shop' => $barberShop,
                        'barbers' => $barber,
                        'services' => $service,
                        'schedules' => $schedule,
                        'appointments' => $appointment,
                    ]
            ]);
        }
    }
}

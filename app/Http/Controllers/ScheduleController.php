<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    private $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedule = Schedule::with('services', 'barbers')->get();

        return response()
                ->json($schedule);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $barberID = $request->barbers_id;
        $barber = Barber::find($barberID);

        if (!$barber) {
            return response()->json([
            'msg' => 'Barber not found'
            ], 404);
        }

        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $services = Service::all();

        $scheduleTimes = [];

        foreach ($services as $service) {
            $serviceID = $service->id;
            $schedule = new Schedule($requestData);
            $schedules = $schedule->getSchedules($service, $barber);

            foreach ($schedules as $scheduleTime) {
                $formattedSchedule = Carbon::createFromFormat('Y-m-d H:i', $scheduleTime);

                $newSchedule = new Schedule([
                'schedule' => $formattedSchedule,
                'services_id' => $serviceID,
                'barbers_id' => $barberID
                ]);

                $newSchedule->save();

                $scheduleTimes[] = $formattedSchedule;
            }
        }

        return response()->json([
        'data' => [
            'msg' => 'Schedules Created Successfully',
            'scheduleTimes' => $scheduleTimes,
            'services' => $services,
            'barber' => $barber
        ]
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        $schedule->services;
        $schedule->barbers;

        return response()
                ->json([
                    'data' => [
                      'schedule' => $schedule,
                    ]
                ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $schedule->update($request->all());

        return response()
                ->json($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $barberShop = Auth::guard('barber_shop')->user();

        if (!$barberShop) {
            abort(403, "Unauthorized");
        }

        $this->schedule = $schedule->delete();

        return response()
                ->json([
                    'data' => [
                        'msg' => 'Deleted Successfully'
                    ]
                ], 204);
    }
}

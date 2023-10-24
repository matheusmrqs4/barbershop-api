<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function createAppointment(Request $request)
    {
        $scheduleID = $request->schedules_id;
        $schedule = Schedule::find($scheduleID);
        $userID = Auth::user()->id;

        if (!$schedule) {
            return response()
            ->json([
                'msg' => 'Schedule Unavailable'
            ], 404);
        }

        $serviceID = $schedule->services_id;
        $barberID = $schedule->barbers_id;

        $appointment = new Appointment([
        'users_id' => $userID,
        'schedules_id' => $schedule->id,
        'services_id' => $serviceID,
        'barbers_id' => $barberID,
        'schedule_time' => $schedule->schedule
        ]);

        $appointment->save();

        $appointment->users()->attach($userID);

        $schedule->delete();

        return response()
        ->json([
            'data' => [
                'msg' => 'Appointment created Successfully',
                'appointment' => $appointment,
                'barber' => $schedule->barbers,
                'service' => $schedule->services,
                'schedule' => $schedule->schedule,
            ]
        ], 201);
    }


    public function showAppointment(Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->id !== $appointment->users_id) {
            abort(403, "Unauthorized");
        }

        $appointment->barbers;
        $appointment->services;

        return response()
        ->json([
            'data' => [
                'msg' => 'Appointment Details',
                'appointment' => $appointment,
            ]
        ], 201);
    }
}

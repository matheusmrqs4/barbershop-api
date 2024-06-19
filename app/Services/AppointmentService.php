<?php

namespace App\Services;

use App\Models\API\Entity\Appointment;
use App\Models\API\Entity\Barber;
use App\Models\API\Entity\Schedule;
use App\Notifications\AppointmentConfirmationNotification;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    public function createAppointment($scheduleID)
    {
        $schedule = Schedule::find($scheduleID);
        $user = Auth::user();
        $userID = $user->id;

        if (!$schedule) {
            return [
                'status' => false,
                'message' => 'Schedule unavailable'
            ];
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

        $barber = Barber::find($barberID);
        if (!$barber) {
            return [
                'status' => false,
                'message' => 'Error finding associated barber'
            ];
        }

        $barbershop = $barber->barbershop;
        if (!$barbershop) {
            return [
                'status' => false,
                'message' => 'Error finding associated barbershop'
            ];
        }

        try {
            $barbershop->notify(new NewAppointmentNotification($appointment));
        } catch (\Exception $e) {
            Log::error('Error sending barbershop notification email: ' . $e->getMessage());
            return [
                'status' => true,
                'message' => 'Appointment created, but failed to send barbershop notification email' . $e->getMessage()
            ];
        }

        try {
            $user->notify(new AppointmentConfirmationNotification($appointment));
        } catch (\Exception $e) {
            Log::error('Error sending user confirmation email: ' . $e->getMessage());
            return [
                'status' => true,
                'message' => 'Appointment created, but failed to send user confirmation email'
            ];
        }

        return [
            'status' => true,
            'message' => 'Appointment created successfully',
            'appointment' => $appointment,
            'barber' => $barber,
            'service' => $schedule->services,
            'schedule' => $schedule->schedule
        ];
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'schedule',
        'services_id',
        'barbers_id'
    ];

    public function services()
    {
        return $this->belongsTo(Service::class, 'services_id');
    }

    public function barbers()
    {
        return $this->belongsTo(Barber::class, 'barbers_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'schedules_id');
    }

    public function getSchedules(Service $service, Barber $barber)
    {
        $duration = $service->duration;
        $serviceSchedules = [];

        $start_time = Carbon::createFromTimeString($barber->start_time);
        $end_time = Carbon::createFromTimeString($barber->end_time);

        $current_date = Carbon::now();
        $end_date = $current_date->copy()->addDays(3); //add days

        while ($current_date->lte($end_date)) {
            $current_time = $start_time->copy();
            while ($current_time->lte($end_time)) {
                if ($current_time->dayOfWeek !== 0) {
                    $serviceSchedules[] = $current_date->format('Y-m-d') . ' ' . $current_time->format('H:i');
                }
                $current_time->addMinutes($duration);
            }
            $current_date->addDay();
        }

        return $serviceSchedules;
    }
}

<?php

namespace App\Models\API\Entity;

use App\Models\API\Auth\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedules_id',
        'users_id',
        'services_id',
        'barbers_id',
        'schedule_time'
    ];

    public function schedules()
    {
        return $this->belongsTo(Schedule::class, 'schedules_id');
    }

    public function barbers()
    {
        return $this->belongsTo(Barber::class, 'barbers_id');
    }

    public function services()
    {
        return $this->belongsTo(Service::class, 'services_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'appointment_user', 'appointments_id', 'users_id');
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class, 'appointments_id');
    }
}

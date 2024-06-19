<?php

namespace App\Models\API\Entity;

use App\Models\API\Auth\BarberShop\BarberShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'social_media',
        'barber_shops_id'
    ];

    public function barberShop()
    {
        return $this->belongsTo(BarberShop::class, 'barber_shops_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'barbers_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'barbers_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'barbers_id');
    }

    public function evaluations()
    {
        return $this->hasManyThrough(Evaluation::class, Appointment::class, 'barbers_id', 'appointments_id', 'id', 'id');
    }
}

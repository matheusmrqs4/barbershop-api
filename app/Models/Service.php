<?php

namespace App\Models;

use App\Models\Barber;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'duration',
        'price',
        'barbers_id'
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class, 'barbers_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'services_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'services_id');
    }
}

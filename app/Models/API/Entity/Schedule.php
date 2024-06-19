<?php

namespace App\Models\API\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}

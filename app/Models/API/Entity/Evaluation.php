<?php

namespace App\Models\API\Entity;

use App\Models\API\Auth\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'grade',
        'appointments_id',
        'users_id'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointments_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function barber()
    {
        return $this->hasOneThrough(Barber::class, Appointment::class, 'id', 'id', 'appointments_id', 'barbers_id');
    }
}

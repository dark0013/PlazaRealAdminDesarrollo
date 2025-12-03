<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'athlete_id',
        'court_id',
        'reservation_date',
        'start_time',
        'end_time',
        'duration',
        'status',
        'reservation_type',
        'notes'
    ];
}

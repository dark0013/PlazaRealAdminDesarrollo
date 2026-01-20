<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Reservations extends Model
{
    use HasFactory;

    protected $table = 'tbl_reservations';

    protected $fillable = [
        'scenario_id',
        'full_name',
        'reservation_date',
        'start_time',
        'end_time',
        'availability',
        'responsable_person',
    ];
}

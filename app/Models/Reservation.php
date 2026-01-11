<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Reservation extends Model
{
    protected $fillable = [
        'sportsman_id',
        'court_id',
        'reservation_date',
        'start_time',
        'end_time',
        'duration',
        'status',
        'reservation_type',
        'notes'
    ];

    /* Constantes de negocio */
    const STATUS_PENDING   = 'PENDING';
    const STATUS_APPROVED  = 'APPROVED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_BLOCKED   = 'BLOCKED';

    const TYPE_NORMAL      = 'NORMAL';
    const TYPE_TOURNAMENT  = 'TOURNAMENT';
    const TYPE_MAINTENANCE = 'MAINTENANCE';
}
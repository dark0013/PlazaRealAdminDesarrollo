<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    use HasFactory;

    protected $table = 'scenario';

    protected $fillable = ['name', 'description', 'location', 'surface_type', 'available_schedule', 'ability', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];
}

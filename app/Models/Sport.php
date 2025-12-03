<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $table = 'sport';
    protected $fillable = ['name', 'description', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];
}

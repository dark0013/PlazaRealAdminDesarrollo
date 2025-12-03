<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';
    protected $fillable = ['name', 'description', 'minimum_age', 'maximum_age', 'applicable_genre', 'status'];

      protected $casts = [
        'status' => 'boolean',
    ];
}

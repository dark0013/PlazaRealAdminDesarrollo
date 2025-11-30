<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sportsman extends Model
{
    protected $table = 'sportsman';

    protected $fillable = [
        'name',
        'surname',
        'identification',
        'birthdate',
        'gender',
        'telephone',
        'email',
        'category',
        'current_ranking',
        'status'
        ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'rol';

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'menu_rol',
            'rol_id',
            'menu_id'
        );
    }
}

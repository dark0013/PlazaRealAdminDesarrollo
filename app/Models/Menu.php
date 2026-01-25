<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';

    protected $fillable = [
        'title',
        'subtitle',
        'icon',
        'type',
        'link',
        'parent_id',
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relación padre → hijos (menú jerárquico)
     */
    public function children()
    {
        return $this
            ->hasMany(Menu::class, 'parent_id')
            ->where('status', true)
            ->orderBy('order');
    }

    /**
     * Relación muchos a muchos con roles
     * PIVOT: menu_rol (menu_id, rol_id)
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'menu_rol',
            'menu_id',
            'rol_id'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $table = 'tournaments';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'tournament_type',
        'mode',
        'category_id',
        'status',
        'description',
        'isTeam',
        'partitioning_amount'
    ];

    /* ======================
       RELACIONES
    ====================== */

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function participants()
    {
        return $this->hasMany(TournamentParticipant::class, 'tournament_id');
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class, 'tournament_id');
    }
}
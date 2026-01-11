<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    protected $table = 'tournament_matches';

    protected $fillable = [
        'tournament_id',
        'player1_id',
        'player2_id',
        'winner_id',
        'round',
        'match_date',
        'start_time',
        'end_time',
        'score'
    ];

    /* ======================
       RELACIONES
    ====================== */

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function player1()
    {
        return $this->belongsTo(Sportsman::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(Sportsman::class, 'player2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Sportsman::class, 'winner_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentParticipant extends Model
{
    protected $table = 'tournament_participants';

    protected $fillable = [
        'tournament_id',
        'sportsman_id',
        'partner_id',
        'teamName'
    ];

    /* ======================
       RELACIONES
    ====================== */

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function sportsman()
    {
        return $this->belongsTo(Sportsman::class, 'sportsman_id');
    }

    public function partner()
    {
        return $this->belongsTo(Sportsman::class, 'partner_id');
    }
}
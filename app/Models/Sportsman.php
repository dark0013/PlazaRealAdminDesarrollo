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

    protected $casts = [
        'status' => 'boolean',
    ];

    public function tournamentParticipations()
    {
        return $this->hasMany(TournamentParticipant::class, 'sportsman_id');
    }

    public function matchesAsPlayer1()
    {
        return $this->hasMany(TournamentMatch::class, 'player1_id');
    }

    public function matchesAsPlayer2()
    {
        return $this->hasMany(TournamentMatch::class, 'player2_id');
    }
}

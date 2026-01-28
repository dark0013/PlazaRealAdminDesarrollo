<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ importar el trait
use Illuminate\Database\Eloquent\Model;


class TournamentMatchs extends Model
{
       use HasFactory;

    protected $table = 'matches';

     protected $fillable = [
        'id',
        'tournament_id',
        'player1_id',
        'player2_id',
        'winner_id',
        'loser_id',
        'round'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
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

    public function loser()
    {
        return $this->belongsTo(Sportsman::class, 'loser_id');
    }
}

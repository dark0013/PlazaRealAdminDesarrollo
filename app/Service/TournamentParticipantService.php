<?php

namespace App\Service;

use App\Models\Tournament;
use App\Models\TournamentParticipant;
use Illuminate\Support\Facades\Validator;

class TournamentParticipantService
{
    /**
     * Registrar participante
     */
    public function register(
        Tournament $tournament,
        int $sportsmanId,
        ?int $partnerId = null
    ): TournamentParticipant {

        if ($tournament->status !== 'ACTIVE') {
            throw ValidationException::withMessages([
                'tournament' => 'El torneo no está activo'
            ]);
        }

        return TournamentParticipant::create([
            'tournament_id' => $tournament->id,
            'sportsman_id'  => $sportsmanId,
            'partner_id'    => $partnerId
        ]);
    }

    /**
     * Quitar participante
     */
    public function remove(TournamentParticipant $participant): void
    {
        $participant->delete();
    }
}
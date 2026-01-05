<?php

namespace App\Service;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Validation\ValidationException;

class TournamentMatchService
{
    /**
     * Crear partido
     */
    public function createMatch(
        Tournament $tournament,
        int $player1Id,
        int $player2Id,
        string $round,
        string $matchDate,
        string $startTime
    ): TournamentMatch {
        if ($tournament->status !== 'ACTIVE') {
            throw ValidationException::withMessages([
                'tournament' => 'El torneo no está activo'
            ]);
        }

        return TournamentMatch::create([
            'tournament_id' => $tournament->id,
            'player1_id' => $player1Id,
            'player2_id' => $player2Id,
            'round' => $round,
            'match_date' => $matchDate,
            'start_time' => $startTime
        ]);
    }

    /**
     * Registrar resultado
     */
    public function registerResult(
        TournamentMatch $match,
        int $winnerId,
        string $score,
        ?string $endTime = null
    ): TournamentMatch {
        if ($match->winner_id) {
            throw ValidationException::withMessages([
                'match' => 'El partido ya tiene resultado'
            ]);
        }

        $match->update([
            'winner_id' => $winnerId,
            'score' => $score,
            'end_time' => $endTime
        ]);

        return $match;
    }
}

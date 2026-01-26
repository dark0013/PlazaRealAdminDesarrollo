<?php

namespace App\Service;

use App\Models\Tournament;
use App\Models\TournamentMatchs;
use Illuminate\Support\Facades\DB;

class TournamentBracketService
{
    /**
     * Genera todos los matches iniciales y placeholders según el torneo
     *
     * @param Tournament $tournament
     * @throws \Exception
     */
    public function generateInitialMatches(Tournament $tournament)
    {
        if ($tournament->status == 1) {
            return ['errors' => 'Los matches de este torneo ya fueron confirmados y no se pueden generar nuevos.'];
        }

        $confirmedExists = TournamentMatchs::where('tournament_id', $tournament->id)
            ->where('status', 1)
            ->exists();

        if ($confirmedExists) {
            return ['errors' => 'Los matches de este torneo ya fueron confirmados y no se pueden generar nuevos.'];
        }

        // Traer solo participantes de este torneo
        $participants = $tournament->participants()->get()->shuffle()->values();
        $totalParticipants = $tournament->partitioning_amount;

        if (!$this->isPowerOfTwo($totalParticipants)) {
            return ['errors' => 'La cantidad de participantes debe ser potencia de 2.'];
        }

        // Borrar matches existentes de este torneo mientras status = 0
        TournamentMatchs::where('tournament_id', $tournament->id)
            ->where('status', 0)
            ->delete();

        $round = 1;

        // Creamos los matches iniciales usando id de tournament_participants
        for ($i = 0; $i < $participants->count(); $i += 2) {
            TournamentMatchs::create([
                'tournament_id' => $tournament->id,
                'round' => $round,
                'player1_id' => $participants[$i]->id,  // <--- id de tournament_participants
                'player2_id' => $participants[$i + 1]->id ?? null,  // <--- id de tournament_participants
                'status' => 0
            ]);
        }

        // Generar placeholders para rondas futuras
        $this->generateFutureRounds($tournament, $round);
    }

    /**
     * Genera rondas futuras vacías según partitioning_amount
     *
     * @param Tournament $tournament
     * @param int $startRound
     */
    private function generateFutureRounds(Tournament $tournament, int $startRound)
    {
        $totalParticipants = $tournament->partitioning_amount;  // usamos partitioning_amount
        $totalRounds = (int) log($totalParticipants, 2);

        for ($round = $startRound + 1; $round <= $totalRounds; $round++) {
            $matchesCount = $totalParticipants / (2 ** $round);

            for ($i = 0; $i < $matchesCount; $i++) {
                TournamentMatchs::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'player1_id' => null,
                    'player2_id' => null,
                    'status' => 0
                ]);
            }
        }
    }

    /** Verifica si un número es potencia de 2 */

    /*
     * private function isPowerOfTwo(int $n): bool
     * {
     *     return $n > 0 && (($n & ($n - 1)) === 0);
     * }
     */

    /**
     * Registra el ganador de un match
     */
    public function recordMatchResult(TournamentMatchs $match, int $winnerId)
    {
        if ($match->status == 1) {
            throw new \Exception('Este match ya fue completado.');
        }

        $match->winner_id = $winnerId;
        $match->loser_id = ($match->player1_id == $winnerId) ? $match->player2_id : $match->player1_id;
        $match->status = 1;
        $match->save();

        $this->assignWinnerToNextRound($match);
    }

    /**
     * Asigna el ganador al match de la siguiente ronda
     */
    private function assignWinnerToNextRound(TournamentMatchs $match)
    {
        $nextRound = $match->round + 1;

        $nextMatch = TournamentMatchs::where('tournament_id', $match->tournament_id)
            ->where('round', $nextRound)
            ->where(function ($q) {
                $q->whereNull('player1_id')->orWhereNull('player2_id');
            })
            ->first();

        if ($nextMatch) {
            if (is_null($nextMatch->player1_id)) {
                $nextMatch->player1_id = $match->winner_id;
            } else {
                $nextMatch->player2_id = $match->winner_id;
            }
            $nextMatch->save();
        }
    }

    private function isPowerOfTwo(int $number): bool
    {
        return ($number != 0) && (($number & ($number - 1)) === 0);
    }

    public function getBracketWithTeams(Tournament $tournament)
    {
        $tournamentId = $tournament->id;

        // SELECT con JOIN para traer los teamName desde tournament_participants
        $matches = DB::table('matches as m')
            ->leftJoin('tournament_participants as p1', function ($join) use ($tournamentId) {
                $join
                    ->on('m.player1_id', '=', 'p1.id')  // <-- Ahora usamos el ID de tournament_participants
                    ->where('p1.tournament_id', $tournamentId);
            })
            ->leftJoin('tournament_participants as p2', function ($join) use ($tournamentId) {
                $join
                    ->on('m.player2_id', '=', 'p2.id')  // <-- Igual aquí
                    ->where('p2.tournament_id', $tournamentId);
            })
            ->leftJoin('tournament_participants as w', function ($join) use ($tournamentId) {
                $join
                    ->on('m.winner_id', '=', 'w.id')  // <-- winner_id también apunta a tournament_participants.id
                    ->where('w.tournament_id', $tournamentId);
            })
            ->leftJoin('tournament_participants as l', function ($join) use ($tournamentId) {
                $join
                    ->on('m.loser_id', '=', 'l.id')  // <-- loser_id igual
                    ->where('l.tournament_id', $tournamentId);
            })
            ->select(
                'm.id as match_id',
                'm.round',
                'p1.teamName as player1_team',
                'p2.teamName as player2_team',
                'w.teamName as winner_team',
                'l.teamName as loser_team',
                'm.status'
            )
            ->where('m.tournament_id', $tournamentId)
            ->orderBy('m.round')
            ->get();

        // Agrupar por ronda
        $bracket = [
            'tournament' => $tournament->name,
            'rounds' => []
        ];

        foreach ($matches->groupBy('round') as $roundNumber => $roundMatches) {
            $bracket['rounds'][] = [
                'round' => $roundNumber,
                'matches' => $roundMatches
            ];
        }

        return $bracket;
    }

    public function confirmMatches(int $tournamentId): int
    {
        // Actualiza todos los matches de este torneo
        $updated = TournamentMatchs::where('tournament_id', $tournamentId)
            ->update(['status' => 1]);

        return $updated;
    }
}

<?php

namespace App\Service;

use App\Models\Tournament;
use App\Models\TournamentMatchs;
use Illuminate\Support\Facades\DB;

class TournamentBracketService
{
    /**
     * Genera automáticamente los encuentros iniciales de un torneo.
     * Si ya existen encuentros, no hace nada.
     *
     * @param Tournament $tournament
     * @return void
     */
    public function generateInitialMatches(Tournament $tournament)
    {
        if ($tournament->matches()->exists()) {
            return; // Ya existen encuentros
        }

        // Mezclar aleatoriamente participantes
        $participants = $tournament->participants->shuffle()->values();
        $round = 1;

        // Crear matches iniciales: cada par de participantes
        for ($i = 0; $i < $participants->count(); $i += 2) {
            TournamentMatchs::create([
                'tournament_id' => $tournament->id,
                'round' => $round,
                'player1_id' => $participants[$i]->id,
                'player2_id' => $participants[$i + 1]->id,
            ]);
        }

        // Generar automáticamente las rondas futuras vacías
        $this->generateFutureRounds($tournament, $round);
    }

    /**
     * Genera todas las rondas futuras en blanco según la cantidad de participantes (n^2)
     * Esto permite que el frontend pueda dibujar el bracket completo desde el inicio.
     *
     * @param Tournament $tournament
     * @param int $startRound
     * @return void
     */
    public function generateFutureRounds(Tournament $tournament, int $startRound)
    {
        $totalParticipants = $tournament->participants->count();
        $totalRounds = (int) log($totalParticipants, 2);

        for ($round = $startRound + 1; $round <= $totalRounds; $round++) {
            $matchesCount = $totalParticipants / (2 ** $round);

            for ($i = 0; $i < $matchesCount; $i++) {
                TournamentMatchs::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'player1_id' => null,
                    'player2_id' => null,
                ]);
            }
        }
    }

    /**
     * Registra el ganador de un match y actualiza automáticamente el siguiente match
     *
     * @param TournamentMatchs $match
     * @param int $winnerId
     * @return void
     */
    public function recordMatchResult(TournamentMatchs $match, int $winnerId)
    {
        if (!in_array($winnerId, [$match->player1_id, $match->player2_id])) {
            throw new \Exception('Jugador inválido para este match');
        }

        DB::transaction(function () use ($match, $winnerId) {
            $match->winner_id = $winnerId;
            $match->loser_id = $match->player1_id == $winnerId ? $match->player2_id : $match->player1_id;
            $match->save();

            // Actualizar siguiente ronda si existe
            $this->updateNextRound($match);
        });
    }

    /**
     * Actualiza el siguiente match con los ganadores de la ronda actual
     *
     * @param TournamentMatchs $match
     * @return void
     */
    protected function updateNextRound(TournamentMatchs $match)
    {
        $tournament = $match->tournament;
        $currentRound = $match->round;
        $nextRound = $currentRound + 1;

        // Traer matches de la ronda siguiente que todavía no tienen jugadores
        $nextRoundMatches = $tournament->matches()
            ->where('round', $nextRound)
            ->whereNull('player1_id')
            ->orWhereNull('player2_id')
            ->orderBy('id')
            ->get();

        // Tomar ganadores de la ronda actual
        $currentRoundWinners = $tournament->matches()
            ->where('round', $currentRound)
            ->whereNotNull('winner_id')
            ->pluck('winner_id')
            ->all();

        // Asignar ganadores de a pares a los matches de la siguiente ronda
        $index = 0;
        foreach ($nextRoundMatches as $nextMatch) {
            if ($index < count($currentRoundWinners)) {
                if (!$nextMatch->player1_id) {
                    $nextMatch->player1_id = $currentRoundWinners[$index++];
                }
                if ($index < count($currentRoundWinners) && !$nextMatch->player2_id) {
                    $nextMatch->player2_id = $currentRoundWinners[$index++];
                }
                $nextMatch->save();
            }
        }
    }

    /**
     * Retorna todos los matches agrupados por ronda para mostrar el bracket
     *
     * @param Tournament $tournament
     * @return array
     */
    public function getBracket(Tournament $tournament)
    {
        $tournament->load(['matches.player1', 'matches.player2', 'matches.winner']);

        return $tournament->matches->groupBy('round')->map(function ($round) {
            return $round->map(function ($match) {
                return [
                    'id' => $match->id,
                    'player1' => $match->player1 ? ['id' => $match->player1->id, 'name' => $match->player1->name] : null,
                    'player2' => $match->player2 ? ['id' => $match->player2->id, 'name' => $match->player2->name] : null,
                    'winner' => $match->winner ? ['id' => $match->winner->id, 'name' => $match->winner->name] : null,
                ];
            });
        })->toArray();
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentMatchs;
use App\Service\TournamentBracketService;
use Illuminate\Http\Request;

class TournamentBracketController extends Controller
{
    protected $bracketService;

    public function __construct(TournamentBracketService $bracketService)
    {
        $this->bracketService = $bracketService;
    }

    /**
     * Genera los matches iniciales y las rondas vacías
     */
    public function generateInitialMatches($tournamentId)
    {
        $tournament = Tournament::with('participants')->findOrFail($tournamentId);
        $result = $this->bracketService->generateInitialMatches($tournament);
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }
        return ResponseHelper::success($result, 'Encuentros iniciales generados', 201);
    }

    /**
     * Mostrar el bracket completo
     */
    public function show($tournamentId)
    {
        $tournament = Tournament::findOrFail($tournamentId);
        $bracket = $this->bracketService->getBracket($tournament);

        return response()->json([
            'tournament' => $tournament,
            'bracket' => $bracket
        ]);
    }

    /**
     * Registrar ganador de un match
     */
    public function recordMatchResult(Request $request, $matchId)
    {
        $match = TournamentMatchs::findOrFail($matchId);
        $winnerId = $request->input('winner_id');

        $this->bracketService->recordMatchResult($match, $winnerId);

        return response()->json(['message' => 'Resultado registrado correctamente']);
    }

    public function showBracket($tournamentId)
    {
        $tournament = Tournament::findOrFail($tournamentId);

        $bracket = $this->bracketService->getBracketWithTeams($tournament);

        if (empty($bracket)) {
            return ResponseHelper::error('No hay rondas disponibles', 404);
        }

        return ResponseHelper::success($bracket);
    }

    public function confirmMatches($tournamentId)
    {
        $tournament = Tournament::findOrFail($tournamentId);

        $updatedCount = $this->bracketService->confirmMatches($tournament->id);

        if (isset($updatedCount['errors'])) {
            return ResponseHelper::error($updatedCount['errors'], 400);
        }
        return ResponseHelper::success($updatedCount, 'Matches confirmados correctamente.', 201);
    }

    public function nextRoundVersus($tournamentId)
    {
        $tournament = Tournament::findOrFail($tournamentId);

        $data = $this->bracketService->getNextRoundVersus($tournament);

        return response()->json($data);
    }

    public function updateResult(Request $request, $tournamentId, $matchId)
{
  //  return $request->all();
    $result = $this->bracketService->setMatchResult(
        $tournamentId,
        $matchId,
        $request->winner_id,
        $request->loser_id,
        $request->id_round,
        $request->punto_player1,
        $request->punto_player2
    );

    if (isset($result['error'])) {
        return ResponseHelper::error($result['error'], 400);
    }

    return ResponseHelper::success($result, 'Match actualizado');
}

}

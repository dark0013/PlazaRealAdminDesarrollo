<?php

namespace App\Http\Controllers\api;

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
        $this->bracketService->generateInitialMatches($tournament);

        return response()->json(['message' => 'Encuentros iniciales generados']);
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
}

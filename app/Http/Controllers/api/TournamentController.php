<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentParticipant;
use App\Service\TournamentMatchService;
use App\Service\TournamentParticipantService;
use App\Service\TournamentService;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    protected TournamentService $tournamentService;
    protected TournamentParticipantService $participantService;
    protected TournamentMatchService $matchService;

    public function __construct(
        TournamentService $tournamentService,
        TournamentParticipantService $participantService,
        TournamentMatchService $matchService
    ) {
        $this->tournamentService = $tournamentService;
        $this->participantService = $participantService;
        $this->matchService = $matchService;
    }

    /**
     * Listar torneos
     */
    public function index()
    {
        $listTournaments = $this->tournamentService->listTournaments();
        if (empty($listTournaments)) {
            return response()->json(['message' => 'No hay torneos registrados'], 404);
        }
        return ResponseHelper::success($listTournaments);
    }

    public function getTournamentById(int $id)
    {
        $result = $this->tournamentService->getTournamentById($id);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 404);
        }

        return ResponseHelper::success($result);
    }

    /**
     * Crear torneo
     */
    public function store(Request $request)
    {
        $tournament = $this->tournamentService->create($request->all());
        if (isset($tournament['errors'])) {
            return ResponseHelper::error($tournament['errors'], 400);
        }

        return ResponseHelper::success($tournament, 201);
    }

    /**
     * Ver torneo
     */
    public function show(Tournament $tournament)
    {
        $tournament = $this->tournamentService->findByIdWithParticipantsAndMatches($tournament->id);

        return $tournament;
    }

    /**
     * Actualizar torneo
     */
    public function update(Request $request, Tournament $tournament)
    {
        $tournament = $this->tournamentService->update(
            $tournament,
            $request->all()
        );

        return response()->json($tournament);
    }

    /**
     * Cerrar torneo
     */
    public function close(Tournament $tournament)
    {
        $tournament = $this->tournamentService->close($tournament);

        return response()->json([
            'message' => 'Torneo finalizado correctamente',
            'data' => $tournament
        ]);
    }

    public function reactivateTournament(Tournament $tournament)
    {
        $tournament = $this->tournamentService->reactivateTournament($tournament);

        return response()->json([
            'message' => 'Torneo reactivado correctamente',
            'data' => $tournament
        ]);
    }

    /**
     * Registrar participante
     */
    public function registerParticipant(Request $request)
    {
        $request->validate([
            'tournament_id' => 'required|integer',
            'sportsman_id' => 'required|integer',
            'partner_id' => 'nullable|integer',
            'teamName' => 'required|string'
        ]);

        $result = $this->participantService->register([
            'tournament_id' => (int) $request->tournament_id,
            'sportsman_id' => (int) $request->sportsman_id,
            'partner_id' => $request->partner_id ? (int) $request->partner_id : null,
            'teamName' => $request->teamName,
        ]);

        if (isset($result['errors'])) {
            return response()->json([
                'success' => false,
                'message' => $result['errors']
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ], 201);
    }

    /**
     * Eliminar participante
     */
    public function removeParticipant(TournamentParticipant $participant)
    {
        $this->participantService->remove($participant);

        return response()->json([
            'message' => 'Participante eliminado'
        ]);
    }

    /**
     * Crear partido
     */
    public function createMatch(Request $request, Tournament $tournament)
    {
        $request->validate([
            'player1_id' => 'required|integer',
            'player2_id' => 'required|integer',
            'round' => 'required|string',
            'match_date' => 'required|date',
            'start_time' => 'required'
        ]);

        $match = $this->matchService->createMatch(
            $tournament,
            $request->player1_id,
            $request->player2_id,
            $request->round,
            $request->match_date,
            $request->start_time
        );

        return response()->json($match, 201);
    }

    /**
     * Registrar resultado del partido
     */
    public function registerResult(Request $request, TournamentMatch $match)
    {
        $request->validate([
            'winner_id' => 'required|integer',
            'score' => 'required|string',
            'end_time' => 'nullable'
        ]);

        $match = $this->matchService->registerResult(
            $match,
            $request->winner_id,
            $request->score,
            $request->end_time
        );

        return response()->json($match);
    }
}

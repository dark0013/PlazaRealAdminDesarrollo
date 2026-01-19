<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentParticipant;

use App\Service\TournamentService;
use App\Service\TournamentParticipantService;
use App\Service\TournamentMatchService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

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
        $this->tournamentService  = $tournamentService;
        $this->participantService = $participantService;
        $this->matchService       = $matchService;
    }

    /**
     * Listar torneos
     */
   /*  public function index()
    {
        return response()->json(
            Tournament::orderBy('start_date', 'desc')->get()
        );
    } */
    public function index()
    {
        $listTournaments = $this->tournamentService->listTournaments();
        if (empty($listTournaments)) {
            return response()->json(['message' => 'No hay torneos registrados'], 404);
        }
        return ResponseHelper::success($listTournaments);
    }

    /**
     * Crear torneo
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'tournament_type' => 'required|string',
            'mode'            => 'required|string',
            'category_id'     => 'required|integer',
            'description'     => 'nullable|string'
        ]);

        $tournament = $this->tournamentService->create($request->all());

        return response()->json($tournament, 201);
    }

    /**
     * Ver torneo
     */
    public function show(Tournament $tournament)
    {
        return response()->json($tournament->load([
            'participants',
            'matches'
        ]));
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
            'data'    => $tournament
        ]);
    }

    /**
     * Registrar participante
     */
    public function registerParticipant(Request $request, Tournament $tournament)
    {
        $request->validate([
            'sportsman_id' => 'required|integer',
            'partner_id'   => 'nullable|integer'
        ]);

        $participant = $this->participantService->register(
            $tournament,
            $request->sportsman_id,
            $request->partner_id
        );

        return response()->json($participant, 201);
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
            'round'      => 'required|string',
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
            'score'     => 'required|string',
            'end_time'  => 'nullable'
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
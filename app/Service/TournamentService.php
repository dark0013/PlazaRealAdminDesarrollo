<?php
namespace App\Service;

use App\Models\Tournament;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class TournamentService
{
    public function listTournaments()
    {
        $today = Carbon::today();  // Fecha actual

        // Obtenemos todos los torneos ordenados
        $listTournaments = Tournament::orderBy('start_date', 'desc')->get();

        if ($listTournaments->isEmpty()) {
            return ['errors' => 'No hay torneos registrados'];
        }

        // Recorremos y actualizamos torneos que ya terminaron pero están activos
        foreach ($listTournaments as $tournament) {
            if ($tournament->status === 'ACTIVO' && $today->gt(Carbon::parse($tournament->end_date))) {
                $tournament->status = 'CANCELADO';
                $tournament->save();
            }
        }

        return $listTournaments;
    }

    public function getTournamentById(int $id)
    {
        $tournament = Tournament::find($id);

        if (!$tournament) {
            return ['errors' => 'Torneo no encontrado'];
        }

        // Verificar si el torneo está activo y ya pasó la fecha de finalización
        $today = Carbon::today();
        if ($tournament->status === 'ACTIVO' && $today->gt(Carbon::parse($tournament->end_date))) {
            $tournament->status = 'CANCELADO';
            $tournament->save();
        }

        return $tournament;
    }

    /**
     * Crear torneo
     */
    public function create(array $data): Tournament
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'tournament_type' => 'required|string',
            // 'mode' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $tournament = Tournament::create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'tournament_type' => $data['tournament_type'],
            'mode' => $data['mode'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'status' => 'ACTIVADO',
            'isTeam' => $data['isTeam'],
            'partitioning_amount' => $data['partitioning_amount']
        ]);
        return $tournament ?: ['errors' => 'Error al crear el torneo'];
    }

    /**
     * Actualizar torneo
     */
    public function update(Tournament $tournament, array $data): Tournament
    {
        $tournament->update($data);
        return $tournament;
    }

    /**
     * Cerrar torneo
     */
    public function close(Tournament $tournament): Tournament
    {
        $tournament->update([
            'status' => 'FINALIZADO',
            'end_date' => Carbon::now()->toDateString()
        ]);

        return $tournament;
    }

    public function reactivateTournament(Tournament $tournament): Tournament
    {
        $tournament->update([
            'status' => 'ACTIVADO'
        ]);

        return $tournament;
    }


        public function findByIdWithParticipantsAndMatches(int $tournamentId)
  {
    // 1️⃣ Torneo
    $tournament = DB::table('tournaments')
        ->where('id', $tournamentId)
        ->first();

    if (!$tournament) {
        abort(404, 'Tournament not found');
    }

    // 2️⃣ Participantes con nombres
    $participants = DB::table('tournament_participants as tp')
        ->leftJoin('sportsman as s1', 's1.id', '=', 'tp.sportsman_id')
        ->leftJoin('sportsman as s2', 's2.id', '=', 'tp.partner_id')
        ->where('tp.tournament_id', $tournamentId)
        ->select([
            'tp.id',
            'tp.tournament_id',
            'tp.sportsman_id',
            'tp.partner_id',
            'tp.created_at',
            'tp.updated_at',

            's1.name as sportsman_name',
            's1.surname as sportsman_surname',

            's2.name as partner_name',
            's2.surname as partner_surname',
        ])
        ->get();

    // 3️⃣ Matches
    $matches = DB::table('tournament_matches')
        ->where('tournament_id', $tournamentId)
        ->get();

    // 4️⃣ CÁLCULOS IMPORTANTES
    $totalParticipants = $participants->count();

    $availableSlots = max(
        0,
        $tournament->partitioning_amount - $totalParticipants
    );

    // 5️⃣ RESPUESTA FINAL FORMATEADA
    return [
        'id' => $tournament->id,
        'name' => $tournament->name,
        'start_date' => $tournament->start_date,
        'end_date' => $tournament->end_date,
        'tournament_type' => $tournament->tournament_type,
        'mode' => $tournament->mode,
        'category_id' => $tournament->category_id,
        'status' => $tournament->status,
        'description' => $tournament->description,
        'isTeam' => $tournament->isTeam,
        'partitioning_amount' => $tournament->partitioning_amount,

        // 🔥 NUEVOS CAMPOS
        'total_participants' => $totalParticipants,
        'available_slots' => $availableSlots,

        'created_at' => $tournament->created_at,
        'updated_at' => $tournament->updated_at,
        'participants' => $participants,
        'matches' => $matches
    ];
}

}

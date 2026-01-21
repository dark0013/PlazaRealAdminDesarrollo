<?php
namespace App\Service;

use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TournamentService
{
    public function listTournaments()
    {
        $listTournaments = Tournament::orderBy('start_date', 'desc')->get();
        if (!$listTournaments) {
            return ['errors' => 'No hay torneos registrados'];
        }
        return $listTournaments ?: ['errors' => 'No hay torneos registrados'];
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
            'status' => 'ACTIVE',
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
            'status' => 'FINISHED',
            'end_date' => Carbon::now()->toDateString()
        ]);

        return $tournament;
    }
}

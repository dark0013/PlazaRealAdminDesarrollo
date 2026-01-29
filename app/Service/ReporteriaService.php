<?php

namespace App\Service;

use App\Models\Reservation;
use App\Models\Scenario;
use App\Models\Sportsman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReporteriaService
{
    public function getReporteReservacion(
        int $scenarioId,
        string $startDate,
        string $endDate
    ) {
        return DB::table('tbl_reservations as r')
            ->join('scenario as s', 's.id', '=', 'r.scenario_id')
            ->join('sportsman as sp', 'sp.id', '=', 'r.id_sportmen')
            ->where('r.scenario_id', $scenarioId)
            ->whereBetween('r.reservation_date', [$startDate, $endDate])
            ->select(
                's.name as scenario_name',
                'r.reservation_date',
                DB::raw("
                CASE 
                    WHEN r.end_time IS NOT NULL 
                    THEN CONCAT(r.start_time, ' - ', r.end_time)
                    ELSE r.start_time
                END as reservation_time
            "),
                DB::raw("CONCAT(sp.name, ' ', sp.surname) as sportsman_name")
            )
            ->orderBy('r.reservation_date')
            ->orderBy('r.start_time')
            ->get();
    }



       public function getCuadroResultadosByTournament(int $tournamentId)
    {
        return DB::table('matches as m')
        ->leftJoin('sportsman as p1', 'p1.id', '=', 'm.player1_id')
        ->leftJoin('sportsman as p2', 'p2.id', '=', 'm.player2_id')
        ->leftJoin('sportsman as w', 'w.id', '=', 'm.winner_id')

        // Jugador / Equipo 1
        ->leftJoin('tournament_participants as tp1', function ($join) {
            $join->on('tp1.sportsman_id', '=', 'm.player1_id')
                 ->on('tp1.tournament_id', '=', 'm.tournament_id');
        })

        // Jugador / Equipo 2
        ->leftJoin('tournament_participants as tp2', function ($join) {
            $join->on('tp2.sportsman_id', '=', 'm.player2_id')
                 ->on('tp2.tournament_id', '=', 'm.tournament_id');
        })

        // 🔥 EQUIPO GANADOR
        ->leftJoin('tournament_participants as tpw', function ($join) {
            $join->on('tpw.sportsman_id', '=', 'm.winner_id')
                 ->on('tpw.tournament_id', '=', 'm.tournament_id');
        })

        ->where('m.tournament_id', $tournamentId)

        ->select(
            DB::raw('m.round as fase'),

            DB::raw("
                CASE 
                    WHEN tp1.teamName IS NOT NULL 
                    THEN tp1.teamName
                    ELSE CONCAT(p1.name, ' ', p1.surname)
                END as jugador_1
            "),

            DB::raw("
                CASE 
                    WHEN tp2.teamName IS NOT NULL 
                    THEN tp2.teamName
                    ELSE CONCAT(p2.name, ' ', p2.surname)
                END as jugador_2
            "),

            DB::raw("CONCAT(m.punto_player1, ' - ', m.punto_player2) as marcador"),

            // ✅ GANADOR CORRECTO
            DB::raw("
                CASE 
                    WHEN tpw.teamName IS NOT NULL 
                    THEN tpw.teamName
                    ELSE CONCAT(w.name, ' ', w.surname)
                END as ganador
            ")
        )
        ->orderBy('m.round')
        ->orderBy('m.id')
        ->get();
    }
}

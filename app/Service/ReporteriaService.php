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
        $reservaciones = DB::table('tbl_reservations as r')
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
                DB::raw("CONCAT(sp.name, ' ', sp.surname) as sportsman_name"),
                'r.availability'
            )
            ->orderBy('r.reservation_date')
            ->orderBy('r.start_time')
            ->get();

        if (!$reservaciones || $reservaciones->isEmpty()) {
            return ['errors' => 'No hay datos de reservaciones'];
        }

        return $reservaciones ?: ['errors' => 'No se pudo obtener las reservaciones'];
    }

    public function getCuadroResultadosByTournament(int $tournamentId)
    {
        $dataResult = DB::table('matches as m')
            ->leftJoin('sportsman as p1', 'p1.id', '=', 'm.player1_id')
            ->leftJoin('sportsman as p2', 'p2.id', '=', 'm.player2_id')
            ->leftJoin('sportsman as w', 'w.id', '=', 'm.winner_id')
            // Jugador / Equipo 1
            ->leftJoin('tournament_participants as tp1', function ($join) {
                $join
                    ->on('tp1.sportsman_id', '=', 'm.player1_id')
                    ->on('tp1.tournament_id', '=', 'm.tournament_id');
            })
            // Jugador / Equipo 2
            ->leftJoin('tournament_participants as tp2', function ($join) {
                $join
                    ->on('tp2.sportsman_id', '=', 'm.player2_id')
                    ->on('tp2.tournament_id', '=', 'm.tournament_id');
            })
            // 🔥 EQUIPO GANADOR
            ->leftJoin('tournament_participants as tpw', function ($join) {
                $join
                    ->on('tpw.sportsman_id', '=', 'm.winner_id')
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

        if (!$dataResult || $dataResult->isEmpty()) {
            return ['errors' => 'No hay datos de cuadro de resultados'];
        }

        return $dataResult ?: ['errors' => 'No se pudo obtener el cuadro de resultados'];
    }

    /*     public function getClasificacionDeportistas($category = null, $gender = null)
        {
            $query = DB::table('sportsman as s')
                // Partidos como player1
                ->leftJoin('matches as m1', function ($join) {
                    $join
                        ->on('m1.player1_id', '=', 's.id')
                        ->where('m1.status', 1);
                })
                // Partidos como player2
                ->leftJoin('matches as m2', function ($join) {
                    $join
                        ->on('m2.player2_id', '=', 's.id')
                        ->where('m2.status', 1);
                })
                ->leftJoin('tournament_participants as tp', 'tp.sportsman_id', '=', 's.id')
                ->select(
                    DB::raw("CONCAT(s.name, ' ', s.surname) as deportista"),
                    's.category as categoria',
                    's.gender as genero',
                    DB::raw('
                    COALESCE(SUM(m1.punto_player1), 0) +
                    COALESCE(SUM(m2.punto_player2), 0)
                    as puntos_totales
                '),
                    DB::raw('COUNT(DISTINCT tp.tournament_id) as torneos_participados')
                )
                ->groupBy(
                    's.id',
                    's.name',
                    's.surname',
                    's.category',
                    's.gender'
                );

            // 🔥 FILTRO POR CATEGORÍA
            if ($category) {
                $query->where('s.category', $category);
            }

            // 🔥 FILTRO POR GÉNERO
            if ($gender) {
                $query->where('s.gender', $gender);
            }

            $dataSportsmen = $query
                ->orderByDesc('puntos_totales')
                ->get();

            if (!$dataSportsmen || $dataSportsmen->isEmpty()) {
                return ['errors' => 'No hay datos de clasificacion de deportistas'];
            }

            return $dataSportsmen ?: ['errors' => 'No se pudo obtener las clasificaciones de deportistas'];
        } */

    public function getClasificacionDeportistas(
        $category = null,
        $gender = null,
        $tournamentId = null,
        $startDate = null,
        $endDate = null
    ) {
        $query = DB::table('sportsman as s')
            // Partidos como player1
            ->leftJoin('matches as m1', function ($join) {
                $join
                    ->on('m1.player1_id', '=', 's.id')
                    ->where('m1.status', 1);
            })
            // Partidos como player2
            ->leftJoin('matches as m2', function ($join) {
                $join
                    ->on('m2.player2_id', '=', 's.id')
                    ->where('m2.status', 1);
            })
            // Participantes del torneo
            ->leftJoin('tournament_participants as tp', 'tp.sportsman_id', '=', 's.id')
            // Torneos
            ->leftJoin('tournaments as t', 't.id', '=', 'tp.tournament_id')
            ->select(
                DB::raw("CONCAT(s.name, ' ', s.surname) as deportista"),
                's.category as categoria',
                's.gender as genero',
                DB::raw('
                COALESCE(SUM(m1.punto_player1), 0) +
                COALESCE(SUM(m2.punto_player2), 0)
                as puntos_totales
            '),
                DB::raw('COUNT(DISTINCT tp.tournament_id) as torneos_participados')
            )
            ->groupBy(
                's.id',
                's.name',
                's.surname',
                's.category',
                's.gender'
            );

        /* =========================
           FILTROS EXISTENTES
        ========================== */

        if ($category) {
            $query->where('s.category', $category);
        }

        if ($gender) {
            $query->where('s.gender', $gender);
        }

        /* =========================
           NUEVOS FILTROS
        ========================== */

        // 🔥 FILTRO POR TORNEO
        if ($tournamentId) {
            $query->where('t.id', $tournamentId);
        }

        // 🔥 FILTRO POR FECHA INICIO
        if ($startDate) {
            $query->whereDate('t.start_date', '>=', $startDate);
        }

        // 🔥 FILTRO POR FECHA FIN
        if ($endDate) {
            $query->whereDate('t.end_date', '<=', $endDate);
        }

        $dataSportsmen = $query
            ->orderByDesc('puntos_totales')
            ->get();

        if ($dataSportsmen->isEmpty()) {
            return ['errors' => 'No hay datos de clasificacion de deportistas'];
        }

        return $dataSportsmen;
    }
}

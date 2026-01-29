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
}

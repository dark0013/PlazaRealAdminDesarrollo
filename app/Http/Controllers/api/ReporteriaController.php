<?php

namespace App\Http\Controllers\Api;

use App\Service\ReporteriaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteriaController extends Controller
{
    private ReporteriaService $reporteriaService;

    public function __construct(ReporteriaService $reporteriaService)
    {
        $this->reporteriaService = $reporteriaService;
    }

    public function getReporteReservacion(Request $request)
    {
       
        // ✅ Validación
        $request->validate([
            'scenario_id' => 'required|exists:scenario,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // ✅ Llamada al servicio
        $reservations = $this->reporteriaService->getReporteReservacion(
            $request->scenario_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

        public function getReporteCuadroResultados(Request $request)
    {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id'
        ]);
        
        $data = $this->reporteriaService->getCuadroResultadosByTournament(
            $request->tournament_id
        );

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }
}

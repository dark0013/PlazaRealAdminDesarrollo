<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Service\ReporteriaService;
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

        if (isset($reservations['errors'])) {
            return ResponseHelper::error($reservations['errors'], 400);
        }
        return ResponseHelper::success($reservations);
    }

    public function getReporteCuadroResultados(Request $request)
    {
        $result = $this->reporteriaService->getCuadroResultadosByTournament(
            $request->tournament_id
        );

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }
        return ResponseHelper::success($result);
    }

   /*  public function getReporteClasificacionDeportistas(Request $request)
    {
        $category = $request->category;
        $gender = $request->gender;

        $result = $this->reporteriaService->getClasificacionDeportistas($category, $gender);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }
        return ResponseHelper::success($result);
    } */

    public function getReporteClasificacionDeportistas(Request $request)
    {
        $category = $request->category;
        $gender = $request->gender;
        $tournamentId = $request->tournament_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $result = $this->reporteriaService->getClasificacionDeportistas($category, $gender, $tournamentId, $startDate, $endDate);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }
        return ResponseHelper::success($result);
    }
}

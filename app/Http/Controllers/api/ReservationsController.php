<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Service\ReservationsService;
use Illuminate\Http\Request;

class ReservationsController
{
    public function __construct(
        private ReservationsService $service
    ) {}

    public function getReservationsByIdDate(Request $request, int $id, string $date)
    {
        $result = $this->service->getReservationsByIdDate($id, $date);

        if (isset($result['errors'])) {
            return response()->json([
                'success' => false,
                'message' => $result['errors']
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function store(Request $request)
    {
        /*    $request->validate([
               'scenario_id' => 'required|integer',
               'id_sportmen' => 'required|integer',
               'reservation_date' => 'required|date',
               'start_time' => 'required|date_format:H:i',
               'end_time' => 'nullable|date_format:H:i|after:start_time',
               'responsable_person' => 'nullable|string|max:255',
           ]);  */

        $result = $this->service->createReservation($request->all());

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

    public function releaseReservation(Request $request, int $id, string $date)
    {
        $result = $this->service->releaseReservationsByScenarioAndDate($id, $date);
        if (isset($result['errors'])) {
            return response()->json([
                'success' => false,
                'message' => $result['errors']
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}

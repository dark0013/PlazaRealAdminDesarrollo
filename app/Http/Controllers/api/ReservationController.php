<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Service\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function getReservations()
    {
        $reservations = $this->reservationService->getAllReservations();

        if (empty($reservations)) {
            return ResponseHelper::error('No reservations found', 404);
        }
        return ResponseHelper::success($reservations);
    }

    public function createReservation(Request $request)
    {
        $result = $this->reservationService->createReservation($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateReservation(Request $request, $id)
    {
        $result = $this->reservationService->updateReservation($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }
}

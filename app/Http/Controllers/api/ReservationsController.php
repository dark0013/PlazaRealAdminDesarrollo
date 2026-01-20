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
}

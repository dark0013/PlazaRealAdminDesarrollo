<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Service\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $service
    ) {}

    /* ===================== CREAR ===================== */
    public function store(Request $request)
    {
        $result = $this->service->create($request->all());

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

    /* ===================== LISTAR ===================== */
    public function index()
    {
        $result = $this->service->getAll();

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

    /* ===================== VER ===================== */
    public function show($id)
    {
        $result = $this->service->getById($id);

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

    /* ===================== POR FECHA ===================== */
    public function byDate($date)
    {
        $result = $this->service->getByDate($date);

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

    /* ===================== APROBAR ===================== */
    public function approve($id)
    {
        $result = $this->service->approve($id);

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

    /* ===================== CANCELAR ===================== */
    public function cancel($id)
    {
        $result = $this->service->cancel($id);

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

    /* ===================== REAGENDAR ===================== */
    public function reschedule(Request $request, $id)
    {
        $result = $this->service->reschedule(
            $id,
            $request->start_time,
            $request->end_time
        );

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

    /* ===================== BLOQUEAR ===================== */
    public function block(Request $request)
    {
        $result = $this->service->block($request->all());

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
}

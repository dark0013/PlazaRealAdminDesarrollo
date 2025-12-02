<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Service\SportService;
use Illuminate\Http\Request;

class SportController extends Controller
{
    protected $sportService;

    public function __construct(SportService $sportService)
    {
        $this->sportService = $sportService;
    }

    public function getAllSports()
    {
        $sports = $this->sportService->getAllSports();

        if (empty($sports)) {
            return ResponseHelper::error('No sports found', 404);
        }
        return ResponseHelper::success($sports);
    }

    public function getSportById($id)
    {
        $result = $this->sportService->getSportById($id);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 404);
        }

        return ResponseHelper::success($result);
    }

    public function createSport(Request $request)
    {
        $result = $this->sportService->createSport($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateSport(Request $request, $id)
    {
        $result = $this->sportService->updateSport($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function deactivateSport($id)
    {
        $result = $this->sportService->changeSportStatus($id, false);
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }
        return ResponseHelper::success($result);
    }

    public function activateSport($id)
    {
        $result = $this->sportService->changeSportStatus($id, true);
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }
        return ResponseHelper::success($result);
    }
}

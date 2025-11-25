<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\SportsmanService;
use App\Helpers\ResponseHelper;

class SportsmanController extends Controller
{
    protected $sportsmanService;
    public function __construct(SportsmanService $sportsmanService)
    {
        $this->sportsmanService = $sportsmanService;
    }
    public function getAllSportsman()
    {
        $sportsmen = $this->sportsmanService->getAllSportsman();

        if (empty($sportsmen)) {
            return ResponseHelper::error('No sportsman found', 404);
        }
        return ResponseHelper::success($sportsmen);
    }

    public function getSportsmanById($id)
    {
        $sportsman = $this->sportsmanService->getSportsmanById($id);

        if (isset($sportsman['errors'])) {
            return ResponseHelper::error($sportsman['errors'], 404);
        }

        return ResponseHelper::success($sportsman);
    }

    public function createSportsman(Request $request)
    {
        $result = $this->sportsmanService->createSportsman($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateSportsman(Request $request, $id)
    {
        $result = $this->sportsmanService->updateSportsman($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function deactivateSportsman($id)
    {
        $result = $this->sportsmanService->changeStatusSportsman($id, 'inactive');
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function activateSportsman($id)
    {
        $result = $this->sportsmanService->changeStatusSportsman($id, 'active');
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }
}

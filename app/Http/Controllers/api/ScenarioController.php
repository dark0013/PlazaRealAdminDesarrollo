<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Service\ScenarioService;

class ScenarioController extends Controller
{
    protected $scenarioService; 
    public function __construct(ScenarioService $scenarioService)
    {
        $this->scenarioService = $scenarioService;
    }

    public function getAllScenarios()
    {
        $scenarios = $this->scenarioService->getAllScenarios();

        if (empty($scenarios)) {
            return ResponseHelper::error('No scenarios found', 404);
        }
        return ResponseHelper::success($scenarios);
    }

    public function getScenarioById($id)
    {
        $result = $this->scenarioService->getScenarioById($id);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 404);
        }

        return ResponseHelper::success($result);
    }

    public function createScenario(Request $request)
    {
        $result = $this->scenarioService->createScenario($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateScenario(Request $request, $id)
    {
        $result = $this->scenarioService->updateScenario($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function deactivateScenario($id)
    {
        $result = $this->scenarioService->changeScenarioStatus($id, false);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function activateScenario($id)
    {
        $result = $this->scenarioService->changeScenarioStatus($id, true);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }
}

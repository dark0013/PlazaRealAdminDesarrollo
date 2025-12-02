<?php

namespace App\Service;
use App\Models\Scenario;    
use Illuminate\Support\Facades\Validator;

class ScenarioService
{
   
    public function getAllScenarios()
    {
        $scenarios = Scenario::all();
        return $scenarios->isEmpty() ? null : $scenarios;
    }

    public function getScenarioById(int $id)
    {
        $scenario = Scenario::find($id);
        if (!$scenario) {
            return ['errors' => 'Scenario not found'];
        }
        return $scenario ?: ['errors' => 'Scenario not found'];
    }

    public function createScenario(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:scenario,name',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $scenario = Scenario::create($data);

        return $scenario ?: ['errors' => 'Scenario creation failed'];
    }

    public function updateScenario($id, array $data)
    {
        $scenario = Scenario::find($id);
        if (!$scenario) {
            return ['errors' => 'Scenario not found'];
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|unique:scenario,name,' . $id,
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $scenario->update($data);

        return $scenario ?: ['errors' => 'Scenario update failed'];
    }

    public function changeScenarioStatus($id, $status)
    {
        $scenario = Scenario::find($id);
        if (!$scenario) {
            return ['errors' => 'Scenario not found'];
        }

        $scenario->status = $status;
        $scenario->save();

        return $scenario ?: ['errors' => 'Scenario status update failed'];
    }
}
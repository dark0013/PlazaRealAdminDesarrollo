<?php
namespace App\Service;

use App\Models\Sportsman;
use Illuminate\Support\Facades\Validator;

class SportsmanService
{
    public function getAllSportsman()
    {
        $sportsmen = Sportsman::all();
        return $sportsmen->isEmpty() ? null : $sportsmen;
    }

    public function getSportsmanById(int $id)
    {
        $sportsman = Sportsman::find($id);
        if (!$sportsman) {
            return ['errors' => 'Sportsman not found'];
        }
        return $sportsman ?: ['errors' => 'Sportsman not found'];
    }

    public function createSportsman(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'surname' => 'required|string',
            'identification' => 'required|string|unique:sportsman,identification',
            'birthdate' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $sportsman = Sportsman::create($data);
        return $sportsman ?: ['errors' => 'Sportsman creation failed'];
    }

    public function updateSportsman($id, array $data)
    {
        $sportsman = Sportsman::find($id);
        if (!$sportsman) {
            return ['errors' => 'Sportsman not found'];
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string',
            'surname' => 'sometimes|required|string',
            'identification' => 'sometimes|required|string|unique:sportsman,identification,' . $id,
            'birthdate' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $sportsman->update($data);

        return $sportsman ?: ['errors' => 'Sportsman update failed'];
    }

    public function changeStatusSportsman($id, $status)
    {
        $sportsman = Sportsman::find($id);
        if (!$sportsman) {
            return ['errors' => 'Sportsman not found'];
        }

        $sportsman->status = $status;
        $sportsman->save();

        return $sportsman ?: ['errors' => 'Sportsman status change failed'];
    }
}

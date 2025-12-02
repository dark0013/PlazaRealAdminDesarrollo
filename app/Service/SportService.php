<?php

namespace App\Service;
use App\Models\Sport;    
use Illuminate\Support\Facades\Validator;

class SportService
{
    public function getAllSports()
    {
        $sports = Sport::all();
        return $sports->isEmpty() ? null : $sports;
    }

    public function getSportById(int $id)
    {
        $sport = Sport::find($id);
        if (!$sport) {
            return ['errors' => 'Sport not found'];
        }
        return $sport ?: ['errors' => 'Sport not found'];
    }

    public function createSport(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:sport,name',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $sport = Sport::create($data);

        return $sport ?: ['errors' => 'Sport creation failed'];
    }   

    public function updateSport($id, array $data)
    {
        $sport = Sport::find($id);
        if (!$sport) {
            return ['errors' => 'Sport not found'];
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|unique:sport,name,' . $id,
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $sport->update($data);

        return $sport ?: ['errors' => 'Sport update failed'];
    }

    public function changeSportStatus($id, bool $status)
    {
        $sport = Sport::find($id);
        if (!$sport) {
            return ['errors' => 'Sport not found'];
        }

        $sport->status = $status;
        $sport->save();

        return $sport ?: ['errors' => 'Sport status update failed'];
    }   
}
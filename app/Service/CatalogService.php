<?php
namespace App\Service;

use App\Models\Catalog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CatalogService{

    public function getAllCatalogs()
    {
        $catalogs = Catalog::all();
        return $catalogs->isEmpty() ? null : $catalogs;
    }

    public function getCatalogById(int $id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return ['errors' => 'Catalog not found'];
        }
        return $catalog ?: ['errors' => 'Catalog not found'];
    }
    public function createCatalog(array $data)
    {
        $validator = Validator::make($data, [            
            'value' => 'required|string',
            'text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $catalog = Catalog::create($data);

        return $catalog ?: ['errors' => 'Catalog creation failed'];
    }    

    public function updateCatalog(int $id, array $data)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return ['errors' => 'Catalog not found'];
        }

        $validator = Validator::make($data, [            
            'value' => 'sometimes|required|string',
            'text' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $catalog->update($data);

        return $catalog ?: ['errors' => 'Catalog update failed'];
    }

    public function changeStatusCatalog(int $id, string $status)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return ['errors' => 'Catalog not found'];
        }

        $catalog->status = $status;
        $catalog->save();

        return $catalog ?: ['errors' => 'Catalog status update failed'];
    }
}
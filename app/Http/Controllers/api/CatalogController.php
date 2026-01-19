<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Service\CatalogService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
   protected $catalogService;

   public function __construct(CatalogService $catalogService)
   {
       $this->catalogService = $catalogService;
   }

    public function getCatalogs()
    {
         $catalogs = $this->catalogService->getAllCatalogs();
    
         if (empty($catalogs)) {
              return ResponseHelper::error('No catalogs found', 404);
         }
         return ResponseHelper::success($catalogs);
    }

    public function getCatalog($id)
    {
        $result = $this->catalogService->getCatalogById($id);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 404);
        }

        return ResponseHelper::success($result);
    }

    public function getRoles()
    {
        $roles = $this->catalogService->getRolesForCatalogs();

        if (empty($roles)) {
            return ResponseHelper::error('No hay roles registrados', 404);
        }
        return ResponseHelper::success($roles);
    }

    public function getScenarios()
    {
        $scenarios = $this->catalogService->getScenariosForCatalogs();

        if (empty($scenarios)) {
            return ResponseHelper::error('No hay escenarios registrados', 404);
        }
        return ResponseHelper::success($scenarios);
    }

    
    public function getSportsmen()
    {
        $sportsmen = $this->catalogService->getSportManForCatalogs();

        if (empty($sportsmen)) {
            return ResponseHelper::error('No hay deportistas registrados', 404);
        }
        return ResponseHelper::success($sportsmen);
    }

    public function categoriaCatalogs()
    {
        $categories = $this->catalogService->getCategorCatalogs();

         if ($categories === null || $categories->isEmpty()) {
            return ResponseHelper::error('No hay categorías registradas', 404);
        }
        return ResponseHelper::success($categories);
    }

        public function getSportCatalogs()
    {
        $sports = $this->catalogService->getSportCatalogs();

         if ($sports === null || $sports->isEmpty()) {
            return ResponseHelper::error('No hay deportes registrados', 404);
        }
        return ResponseHelper::success($sports);
    }














    public function createCatalog(Request $request)
    {
        $result = $this->catalogService->createCatalog($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateCatalog(Request $request, $id)
    {
        $result = $this->catalogService->updateCatalog($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function desactivateCatalog($id)
    {
        $result = $this->catalogService->changeStatusCatalog($id, 'inactive');

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function activateCatalog($id)
    {
        $result = $this->catalogService->changeStatusCatalog($id, 'active');

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }   

}
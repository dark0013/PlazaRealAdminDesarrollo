<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Service\CategoryService;

class CategoryController extends Controller
{
   protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
         $this->categoryService = $categoryService;
    }

    public function getAllCategories()
    {
        $categories = $this->categoryService->getAllCategories();

        if (empty($categories)) {
            return ResponseHelper::error('No categories found', 404);
        }
        return ResponseHelper::success($categories);
    }

    public function getCategoryById($id)
    {
        $result = $this->categoryService->getCategoryById($id);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 404);
        }

        return ResponseHelper::success($result);
    }

    public function createCategory(Request $request)
    {
        $result = $this->categoryService->createCategory($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $result = $this->categoryService->updateCategory($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function deactivateCategory($id)
    {
        $result = $this->categoryService->changeCategoryStatus($id, false);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function activateCategory($id)
    {
        $result = $this->categoryService->changeCategoryStatus($id, true);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }
}



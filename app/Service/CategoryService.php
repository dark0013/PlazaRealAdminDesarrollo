<?php

namespace App\Service;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryService
{
    public function getAllCategories()
    {
        $categories = Category::all();
        return $categories->isEmpty() ? null : $categories;
    }

    public function getCategoryById(int $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ['errors' => 'Category not found'];
        }
        return $category ?: ['errors' => 'Category not found'];
    }

    public function createCategory(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:category,name',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $category = Category::create($data);

        return $category ?: ['errors' => 'Category creation failed'];
    }

    public function updateCategory($id, array $data)
    {
        $category = Category::find($id);
        if (!$category) {
            return ['errors' => 'Category not found'];
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|unique:category,name,' . $id,
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $category->update($data);

        return $category ?: ['errors' => 'Category update failed'];
    }

    public function changeCategoryStatus($id, bool $status)
    {
        $category = Category::find($id);
        if (!$category) {
            return ['errors' => 'Category not found'];
        }

        $category->status = $status;
        $category->save();

        return $category ?: ['errors' => 'Category status update failed'];
    }
}

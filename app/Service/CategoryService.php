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
            return ['errors' => 'Categoría no encontrada'];
        }
        return $category ?: ['errors' => 'Categoría no encontrada'];
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

        return $category ?: ['errors' => 'Error al crear la categoría'];
    }

    public function updateCategory($id, array $data)
    {
        $category = Category::find($id);
        if (!$category) {
            return ['errors' => 'Categoría no encontrada'];
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|unique:category,name,' . $id,
            'description' => 'sometimes|required|string',
        ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'name.string' => 'El nombre debe ser un texto.',
                'name.unique' => 'El nombre ya está registrado.',
                'description.required' => 'La descripción es obligatoria.',
                'description.string' => 'La descripción debe ser un texto.',
            ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $category->update($data);

        return $category ?: ['errors' => 'La actualización de la categoría falló.'];
    }

    public function changeCategoryStatus($id, bool $status)
    {
        $category = Category::find($id);
        if (!$category) {
            return ['errors' => 'Categoría no encontrada'];
        }

        $category->status = $status;
        $category->save();

        return $category ?: ['errors' => 'La actualización del estado de la categoría falló.'];
    }
}

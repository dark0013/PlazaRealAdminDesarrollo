<?php

namespace App\Service;

use App\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionService
{
    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return $permissions->isEmpty() ? null : $permissions;
    }

    public function getPermissionById(int $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return ['errors' => 'Role not found'];
        }
        return $permission ?: ['errors' => 'Permission not found'];
    }


    public function createPermission(array $data)
    {
        // Validation
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:permission,name',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Create permission
        $permission = Permission::create($data);

        if (!$permission) {
            return ['errors' => 'Permission creation failed'];
        }

        return $permission;
    }

    public function updatePermission(int $id, array $data)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return ['errors' => 'Permission not found'];
        }

        $validator = Validator::make($data, [
            'name' => 'required|string|unique:permission,name,' . $id,
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $permission->update($data);
        return $permission ?: ['errors' => 'Permission update failed'];

    }

   public function changeStatusPermission($id,  $status)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return ['errors' => 'Permission not found'];
        }

        $permission->status = $status;
        $permission->save();

        return $permission ?: ['errors' => 'Permission status change failed'];
    }
}

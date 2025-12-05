<?php

namespace App\Service;
use App\Models\Role;    
use Illuminate\Support\Facades\Validator;
class RoleService
{
    public function getAllRoles()
    {
        $roles = Role::all();
        return $roles->isEmpty() ? null : $roles;
    }

    public function getRoleById(int $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return ['errors' => 'Role not found'];
        }
        return $role ?: ['errors' => 'Role not found'];
    }

    public function createRole(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:rol,name',
            /*'description' => 'string' ,
            'permissions' => 'required|string', */
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $role = Role::create($data);

        return $role ?: ['errors' => 'Role creation failed'];
    }

    public function updateRole($id, array $data)
    {
        $role = Role::find($id);
        if (!$role) {
            return ['errors' => 'Role not found'];
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|unique:rol,name,' . $id,
           /* 'description' => 'string',*/
           /* 'permissions' => 'sometimes|required|string',*/
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $role->update($data);

        return $role ?: ['errors' => 'Role update failed'];
    }

    public function changeStatusRole($id,  $status)
    {
        $role = Role::find($id);
        if (!$role) {
            return ['errors' => 'Role not found'];
        }

        $role->status = $status;
        $role->save();

        return $role ?: ['errors' => 'Role status change failed'];
    }
}
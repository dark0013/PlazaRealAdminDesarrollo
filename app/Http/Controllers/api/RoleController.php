<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\RoleService;
use App\Helpers\ResponseHelper;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function getRoles()
    {
        $roles = $this->roleService->getAllRoles();

        if (empty($roles)) {
            return ResponseHelper::error('No roles found', 404);
        }
        return ResponseHelper::success($roles);
    }

    public function createRole(Request $request)
    {
        $result = $this->roleService->createRole($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updateRole(Request $request, $id)
    {
        $result = $this->roleService->updateRole($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function deactivateRole($id)
    {
        $result = $this->roleService->changeStatusRole($id, 'inactive');
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function activateRole($id)
    {
        $result = $this->roleService->changeStatusRole($id, 'active');
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }
}

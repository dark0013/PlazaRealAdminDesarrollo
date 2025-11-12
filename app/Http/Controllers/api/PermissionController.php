<?php
namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Service\PermissionService;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function getPermissions()
    {
        $permissions = $this->permissionService->getAllPermissions();

        if (empty($permissions)) {
            return ResponseHelper::error('No permissions found', 404);
        }
        return ResponseHelper::success($permissions);
    }

    public function createPermission(Request $request)
    {
        $result = $this->permissionService->createPermission($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 201);
    }

    public function updatePermission(Request $request, $id)
    {
        $result = $this->permissionService->updatePermission($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function getPermission($id)
    {
        $result = $this->permissionService->getPermissionById($id);

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 404);
        }

        return ResponseHelper::success($result);
    }

    public function deactivatePermission($id)
    {
        $result = $this->permissionService->changeStatusPermission($id, false);
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }

    public function activatePermission($id)
    {
        $result = $this->permissionService->changeStatusPermission($id, true);
        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result);
    }
}

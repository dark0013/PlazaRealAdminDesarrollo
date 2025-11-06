<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\UserService;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUsers()
    {
        $users = $this->userService->getAllUsers();
        if (empty($users)) {
            return ResponseHelper::error('No users found', 404);
        }
        return ResponseHelper::success($users);
    }

    public function createUser(Request $request)
    {
        $result = $this->userService->createUser($request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 'User created', 201);
    }

    public function updateUser(Request $request, $id)
    {
        $result = $this->userService->updateUser($id, $request->all());

        if (isset($result['errors'])) {
            return ResponseHelper::error($result['errors'], 400);
        }

        return ResponseHelper::success($result, 'User updated', 200);
    }

    public function deactivateUser($id)
    {
        $user = $this->userService->updateStatus($id, 'inactive');

        return ResponseHelper::success($user, 200);
    }
    public function activateUser($id)
    {
        $user = $this->userService->updateStatus($id, 'active');

        return ResponseHelper::success($user, 200);
    }


}

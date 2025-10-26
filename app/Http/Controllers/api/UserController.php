<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        if (empty($users)) {
            return $this->messageResponse('No users found', 200);
        }
        return $this->messageResponse($users);
    }

    public function createUser(Request $request)
    {
       $validator = validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identification_number' => 'required|string|unique:users,identification_number',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|string|max:50',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->messageResponse($validator->errors(), 400);
        }

        $user = User::create($request->all());
        if (!$user) {
            return $this->messageResponse('User creation failed', 500);
        }
        return $this->messageResponse($user, 201);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user);
    }

    private function messageResponse($message, $status = 200)
    {
        return response()->json(['message' => $message], $status);
    }
}

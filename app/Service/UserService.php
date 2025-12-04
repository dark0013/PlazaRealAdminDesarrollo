<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserService
{
    public function getAllUsers()
    {
        $users = User::all();
        return $users->isEmpty() ? null : $users;
    }

    public function createUser(array $data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'primary_surname' => 'required|string|max:255',
            'secondary_surname' => 'required|string|max:255',
            'identification_number' => 'required|string|unique:users,identification_number',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'string|min:8',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::create($data);

        return $user ?: ['errors' => 'User creation failed'];
    }

    public function updateUser(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'primary_surname' => 'required|string|max:255',
            'secondary_surname' => 'required|string|max:255',
            'identification_number' => 'required|string|unique:users,identification_number',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'string|min:8',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::findOrFail($id);

        $user->update($data);

        return $user;
    }

    public function updateStatus(int $id, string $status)
    {
        $user = User::findOrFail($id);

        $user->status = $status;
        $user->save();

        return $user;
    }
}

<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function login(array $credentials)
    {

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::select(
            DB::raw("CONCAT(first_name, ' ', last_name) as name"),
            'email',
            'avatar',
            'status',
            'role',
            'password',
            'id'
        )
            ->where('email', $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Login successful.',
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'user' => $user,
        ];
    }

    public function logout($user)
    {
        // Delete all tokens for the user
        $user->tokens()->delete();

        return [
            'message' => 'Logout successful.'
        ];
    }
}

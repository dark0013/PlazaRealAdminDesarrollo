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
            'id',
            'first_name',
            DB::raw("CONCAT(primary_surname, ' ', secondary_surname) as full_last_name"),
            'email',
            'avatar',
            'status',
            'role',
            'password',
            'is_temporal',
            
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

<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserService
{
    public function getAllUsers()
    {
        $user = User::select(
            'first_name',
            'primary_surname',
            'secondary_surname',
            'identification_number',
            DB::raw("CONCAT( primary_surname, ' ', secondary_surname) as full_last_name"),
            'email',
            'telephone',
            'avatar',
            'status',
            'is_temporal',
            'role',
            'password',
            'id'
        )->get();

        return $user->isEmpty() ? null : $user;
    }

    public function createUser(array $data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'primary_surname' => 'required|string|max:255',
            'secondary_surname' => 'required|string|max:255',
            'identification_number' => 'required|unique:users,identification_number',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'min:8',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|integer'
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.string' => 'El nombre debe ser un texto válido.',
            'first_name.max' => 'El nombre no puede exceder 255 caracteres.',
            'primary_surname.required' => 'El primer apellido es obligatorio.',
            'primary_surname.string' => 'El primer apellido debe ser un texto válido.',
            'primary_surname.max' => 'El primer apellido no puede exceder 255 caracteres.',
            'secondary_surname.required' => 'El segundo apellido es obligatorio.',
            'secondary_surname.string' => 'El segundo apellido debe ser un texto válido.',
            'secondary_surname.max' => 'El segundo apellido no puede exceder 255 caracteres.',
            'identification_number.required' => 'El número de identificación es obligatorio.',
            'identification_number.unique' => 'El número de identificación ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser un texto válido.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'telephone.string' => 'El número de teléfono debe ser un texto válido.',
            'telephone.max' => 'El número de teléfono no puede exceder 20 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.integer' => 'El rol debe ser un valor numérico válido.',
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
            'identification_number' => 'required|',
            'email' => 'required|string|email|max:255,email',
            //'password' => 'min:8',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|integer'
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.string' => 'El nombre debe ser un texto válido.',
            'first_name.max' => 'El nombre no puede exceder 255 caracteres.',
            'primary_surname.required' => 'El primer apellido es obligatorio.',
            'primary_surname.string' => 'El primer apellido debe ser un texto válido.',
            'primary_surname.max' => 'El primer apellido no puede exceder 255 caracteres.',
            'secondary_surname.required' => 'El segundo apellido es obligatorio.',
            'secondary_surname.string' => 'El segundo apellido debe ser un texto válido.',
            'secondary_surname.max' => 'El segundo apellido no puede exceder 255 caracteres.',
            'identification_number.required' => 'El número de identificación es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser un texto válido.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'telephone.string' => 'El número de teléfono debe ser un texto válido.',
            'telephone.max' => 'El número de teléfono no puede exceder 20 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.integer' => 'El rol debe ser un valor numérico válido.',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::findOrFail($id);

        unset($data['password']);
        
        $user->update($data);

        return $user;
    }

    public function updateStatus(int $id, $status)
    {
        $user = User::findOrFail($id);

        Log::info('Datos del usuario:', ['user.status' => $user->status, 'status' => $status]);

        $user->status = $status;
        $user->save();

        return $user;
    }
}

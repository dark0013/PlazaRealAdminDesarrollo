<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
                'email' => ['Credenciales invalidas.'],
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

    /*    public function sendResetPasswordEmail(string $email)
       {
           $user = User::where('email', $email)->first();

           if (!$user) {
               return false;  // Por seguridad, no decimos si no existe
           }

           // Generar token en texto plano
           $token = Str::random(64);

           // Guardar token hasheado en DB
           DB::table('password_reset_tokens')->updateOrInsert(
               ['email' => $email],
               [
                   'token' => Hash::make($token),
                   'created_at' => now()
               ]
           );

           // Crear link de reset
           $resetLink = url('/reset-password?token=' . $token . '&email=' . $email);

           // Enviar correo
           Mail::raw(
               "Hola {$user->name},\n\nHaz clic en el siguiente enlace para restablecer tu contraseña:\n\n$resetLink\n\nEste enlace expira en 60 minutos.",
               function ($message) use ($email) {
                   $message
                       ->to($email)
                       ->subject('Recuperación de contraseña');
               }
           );

           return true;
       } */

    public function resetPassword(string $email, string $token, string $password)
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record || !Hash::check($token, $record->token)) {
            throw new \Exception('Token inválido o expirado');
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            throw new \Exception('Token expirado');
        }

        User::where('email', $email)->update([
            'password' => Hash::make($password)
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }

    // //olvide contrasena
    public function sendResetPasswordEmail(string $email)
    {
        // Buscar usuario
        $user = User::where('email', $email)->first();

        // Por seguridad, no decimos si no existe
        if (!$user) {
            return false;
        }

        // Generar token aleatorio
        $token = Str::random(64);

        // Guardar token hasheado en la tabla password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Link de reset (para pruebas)
        $resetLink = url('/reset-password?token=' . $token . '&email=' . $email);

        // Enviar correo
        Mail::raw(
            "Hola {$user->name},\n\nHaz clic en el siguiente enlace para restablecer tu contraseña:\n\n$resetLink\n\nEste enlace expira en 60 minutos.",
            function ($message) use ($email) {
                $message
                    ->to($email)
                    ->subject('Recuperación de contraseña');
            }
        );

        return true;
    }

    public function sendChangePasswordEmail($usuario_id,
        $email,
        $password,
        $new_password)
    {

    
        // Buscar usuario
        $email = strtolower(trim($email));

        $user = User::where('id', $usuario_id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        


        // Por seguridad, no decimos si no existe
        if (!$user) {
            return false;
        }

        // Actualizar password (SIEMPRE hasheado) e is_temporal
        $user->password = Hash::make($new_password);
        $user->is_temporal = false;
        $user->save();

        // Enviar correo
        Mail::raw(
            "Hola {$user->name},\n\nTu contraseña ha sido cambiada exitosamente.",
            function ($message) use ($email) {
                $message
                    ->to($email)
                    ->subject('Cambio de contraseña');
            }
        );

        return $user;
    }
}

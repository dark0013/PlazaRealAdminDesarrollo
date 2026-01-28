<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

      public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $this->authService->sendResetPasswordEmail($request->email);

        return response()->json([
            'message' => 'Si el correo existe, se enviará un enlace de recuperación'
        ]);
    }

    // POST /reset-password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8'
        ]);

        try {
            $this->authService->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );

            return response()->json(['message' => 'Contraseña actualizada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


  /*   public function resetPassword(Request $request)
    {

      //  return "hola";
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json([
                'message' => 'Token inválido o expirado'
            ], 400);
        }

        // Expiración (60 min)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return response()->json([
                'message' => 'Token expirado'
            ], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ]);
    } */



}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $user = $request->user(); // Obtener usuario autenticado
        $result = $this->authService->logout($user);

        return response()->json($result);
    }

     public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $this->authService->sendChangePasswordEmail($request->email);

        return response()->json([
            'message' => 'Si el correo existe, se enviará un enlace de recuperación'
        ]);
    }




}

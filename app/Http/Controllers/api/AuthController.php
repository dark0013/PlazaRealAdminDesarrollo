<?php

namespace App\Http\Controllers\api;

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
}

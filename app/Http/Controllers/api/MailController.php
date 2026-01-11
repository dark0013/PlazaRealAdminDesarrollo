<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'mensaje' => 'required|string'
        ]);

        Mail::to($request->correo)->send(new NotificacionMail($request->mensaje));

        return response()->json([
            'status' => 'ok',
            'message' => 'Correo enviado correctamente'
        ], 200);
    }
}

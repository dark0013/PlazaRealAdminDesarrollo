<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $user = User::create($request->all());
        return response()->json($user, 201);
    }
}

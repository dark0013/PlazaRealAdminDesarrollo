<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::get('/user', [UserController::class, 'getUsers']);

Route::post('/user', [UserController::class, 'createUser']);

Route::put('/user/{id}', [UserController::class, 'updateUser']);



/* 



Route::delete('/user', function () {
    return "????";
});
 */
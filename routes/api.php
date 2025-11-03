<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\API\AuthController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'getUsers']);
    Route::post('/', [UserController::class, 'createUser']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::patch('/{id}/desactivate', [UserController::class, 'deactivateUser']);
    Route::patch('/{id}/activate', [UserController::class, 'activateUser']);
});




Route::middleware('auth:sanctum')->prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'getRoles']);
    Route::post('/', [RoleController::class, 'createRole']);
    Route::put('/{id}', [RoleController::class, 'updateRole']);
    Route::patch('/{id}/desactivate', [RoleController::class, 'deactivateRole']);
    Route::patch('/{id}/activate', [RoleController::class, 'activateRole']);
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
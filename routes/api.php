<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\RoleController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

/* Route::get('/user', [UserController::class, 'getUsers']);

Route::post('/user', [UserController::class, 'createUser']);

Route::put('/user/{id}', [UserController::class, 'updateUser']);

Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivateUser']);

Route::patch('/users/{id}/activate', [UserController::class, 'activateUser']); */

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'getUsers']);
    Route::post('/', [UserController::class, 'createUser']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::patch('/{id}/desactivate', [UserController::class, 'deactivateUser']);
    Route::patch('/{id}/activate', [UserController::class, 'activateUser']);
});


//role

Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'getRoles']);
    Route::post('/', [RoleController::class, 'createRole']); 
    Route::put('/{id}', [RoleController::class, 'updateRole']);
    Route::patch('/{id}/desactivate', [RoleController::class, 'deactivateRole']);
    Route::patch('/{id}/activate', [RoleController::class, 'activateRole']);
});

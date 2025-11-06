<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;

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

Route::middleware('auth:sanctum')->prefix('permission')->group(function () {
    Route::get('/', [PermissionController::class, 'getPermissions']);
    Route::post('/', [PermissionController::class, 'createPermission']);
    Route::put('/{id}', [PermissionController::class, 'updatePermission']);
    Route::get('/{id}', [PermissionController::class, 'getPermission']);
    Route::patch('/{id}/desactivate', [PermissionController::class, 'deactivatePermission']);
    Route::patch('/{id}/activate', [PermissionController::class, 'activatePermission']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
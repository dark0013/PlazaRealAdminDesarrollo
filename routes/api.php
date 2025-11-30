<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'getUsers']);
    Route::post('/', [UserController::class, 'createUser']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::patch('/{id}/deactivate', [UserController::class, 'deactivateUser']);
    Route::patch('/{id}/activate', [UserController::class, 'activateUser']);
});

Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'getRoles']);
    Route::post('/', [RoleController::class, 'createRole']);
    Route::put('/{id}', [RoleController::class, 'updateRole']);
    Route::patch('/{id}/deactivate', [RoleController::class, 'deactivateRole']);
    Route::patch('/{id}/activate', [RoleController::class, 'activateRole']);
});

Route::middleware('auth:sanctum')->prefix('permission')->group(function () {
    Route::get('/', [PermissionController::class, 'getPermissions']);
    Route::post('/', [PermissionController::class, 'createPermission']);
    Route::put('/{id}', [PermissionController::class, 'updatePermission']);
    Route::get('/{id}', [PermissionController::class, 'getPermission']);
    Route::patch('/{id}/deactivate', [PermissionController::class, 'deactivatePermission']);
    Route::patch('/{id}/activate', [PermissionController::class, 'activatePermission']);
});

Route::prefix('catalogs')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CatalogController::class, 'getCatalogs']);
    Route::get('/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'getCatalog']);
    Route::post('/', [\App\Http\Controllers\Api\CatalogController::class, 'createCatalog']);
    Route::put('/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'updateCatalog']);
   /*  Route::patch('/{id}/desactivate', [\App\Http\Controllers\Api\CatalogController::class, 'desactivateCatalog']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\CatalogController::class, 'activateCatalog']); */
});

//administración de deportistas
Route::prefix('sportsman')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\SportsmanController::class, 'getAllSportsman']);
    Route::get('/{id}', [\App\Http\Controllers\Api\SportsmanController::class, 'getSportsmanById']);
    Route::post('/', [\App\Http\Controllers\Api\SportsmanController::class, 'createSportsman']);
    Route::put('/{id}', [\App\Http\Controllers\Api\SportsmanController::class, 'updateSportsman']);
    Route::patch('/{id}/desactivate', [\App\Http\Controllers\Api\SportsmanController::class, 'deactivateSportsman']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\SportsmanController::class, 'activateSportsman']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
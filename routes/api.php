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
    Route::patch('/{id}/desactivate', [UserController::class, 'deactivateUser']);
    Route::patch('/{id}/activate', [UserController::class, 'activateUser']);
});

Route::prefix('roles')->group(function () {
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

Route::prefix('sports')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\SportController::class, 'getAllSports']);
    Route::get('/{id}', [\App\Http\Controllers\Api\SportController::class, 'getSportById']);
    Route::post('/', [\App\Http\Controllers\Api\SportController::class, 'createSport']);
    Route::put('/{id}', [\App\Http\Controllers\Api\SportController::class, 'updateSport']);
    Route::patch('/{id}/desactivate', [\App\Http\Controllers\Api\SportController::class, 'deactivateSport']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\SportController::class, 'activateSport']);
});

Route::prefix('scenarios')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ScenarioController::class, 'getAllScenarios']);
    Route::get('/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'getScenarioById']);
    Route::post('/', [\App\Http\Controllers\Api\ScenarioController::class, 'createScenario']);
    Route::put('/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'updateScenario']);
    Route::patch('/{id}/desactivate', [\App\Http\Controllers\Api\ScenarioController::class, 'deactivateScenario']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\ScenarioController::class, 'activateScenario']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, 'getAllCategories']);
    Route::get('/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'getCategoryById']);
    Route::post('/', [\App\Http\Controllers\Api\CategoryController::class, 'createCategory']);
    Route::put('/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'updateCategory']);
    Route::patch('/{id}/desactivate', [\App\Http\Controllers\Api\CategoryController::class, 'deactivateCategory']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\CategoryController::class, 'activateCategory']);
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
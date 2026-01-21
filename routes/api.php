<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReservationsController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/roles', [\App\Http\Controllers\Api\CatalogController::class, 'getRoles']);
    Route::get('/sportsmen', [\App\Http\Controllers\Api\CatalogController::class, 'getSportsmen']);
    Route::get('/scenarios', [\App\Http\Controllers\Api\CatalogController::class, 'getScenarios']);
    Route::get('/categories', [\App\Http\Controllers\Api\CatalogController::class, 'categoriaCatalogs']);
    // Route::get('/categories', [\App\Http\Controllers\Api\CatalogController::class, 'categoriaCatalogs']);
    Route::get('/sports', [\App\Http\Controllers\Api\CatalogController::class, 'getSportCatalogs']);

    Route::get('/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'getCatalog']);
    Route::post('/', [\App\Http\Controllers\Api\CatalogController::class, 'createCatalog']);
    Route::put('/{id}', [\App\Http\Controllers\Api\CatalogController::class, 'updateCatalog']);
    /*  Route::patch('/{id}/deactivate', [\App\Http\Controllers\Api\CatalogController::class, 'deactivateCatalog']);
     Route::patch('/{id}/activate', [\App\Http\Controllers\Api\CatalogController::class, 'activateCatalog']); */
});

// administración de deportistas
Route::prefix('sportsman')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\SportsmanController::class, 'getAllSportsman']);
    Route::get('/{id}', [\App\Http\Controllers\Api\SportsmanController::class, 'getSportsmanById']);
    Route::post('/', [\App\Http\Controllers\Api\SportsmanController::class, 'createSportsman']);
    Route::put('/{id}', [\App\Http\Controllers\Api\SportsmanController::class, 'updateSportsman']);
    Route::patch('/{id}/deactivate', [\App\Http\Controllers\Api\SportsmanController::class, 'deactivateSportsman']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\SportsmanController::class, 'activateSportsman']);
});

Route::prefix('sports')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\SportController::class, 'getAllSports']);
    Route::get('/{id}', [\App\Http\Controllers\Api\SportController::class, 'getSportById']);
    Route::post('/', [\App\Http\Controllers\Api\SportController::class, 'createSport']);
    Route::put('/{id}', [\App\Http\Controllers\Api\SportController::class, 'updateSport']);
    Route::patch('/{id}/deactivate', [\App\Http\Controllers\Api\SportController::class, 'deactivateSport']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\SportController::class, 'activateSport']);
});

Route::prefix('scenarios')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ScenarioController::class, 'getAllScenarios']);
    Route::get('/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'getScenarioById']);
    Route::post('/', [\App\Http\Controllers\Api\ScenarioController::class, 'createScenario']);
    Route::put('/{id}', [\App\Http\Controllers\Api\ScenarioController::class, 'updateScenario']);
    Route::patch('/{id}/deactivate', [\App\Http\Controllers\Api\ScenarioController::class, 'deactivateScenario']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\ScenarioController::class, 'activateScenario']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, 'getAllCategories']);
    Route::get('/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'getCategoryById']);
    Route::post('/', [\App\Http\Controllers\Api\CategoryController::class, 'createCategory']);
    Route::put('/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'updateCategory']);
    Route::patch('/{id}/deactivate', [\App\Http\Controllers\Api\CategoryController::class, 'deactivateCategory']);
    Route::patch('/{id}/activate', [\App\Http\Controllers\Api\CategoryController::class, 'activateCategory']);
});

Route::prefix('reservations')->group(function () {
    Route::get('/', [ReservationController::class, 'index']);
    Route::get('{id}', [ReservationController::class, 'show']);
    Route::get('by-date/{date}', [ReservationController::class, 'byDate']);

    Route::post('/', [ReservationController::class, 'store']);
    Route::patch('{id}/approve', [ReservationController::class, 'approve']);
    Route::patch('{id}/cancel', [ReservationController::class, 'cancel']);
    Route::patch('{id}/reschedule', [ReservationController::class, 'reschedule']);
    Route::post('/block', [ReservationController::class, 'block']);
});

Route::prefix('reservationsx')->group(function () {
    Route::get('by-date/{id}/{date}', [ReservationsController::class, 'getReservationsByIdDate']);
    Route::post('/', [ReservationsController::class, 'store']);
    Route::delete('/{id}/{date}', [ReservationsController::class, 'releaseReservation']);
});

Route::prefix('admin/tournaments')->group(function () {
    Route::get('/', [TournamentController::class, 'index']);
    Route::post('/', [TournamentController::class, 'store']);
    Route::get('/{tournament}', [TournamentController::class, 'show']);
    Route::put('/{tournament}', [TournamentController::class, 'update']);
    Route::post('/{tournament}/close', [TournamentController::class, 'close']);

    Route::post('/{tournament}/participants', [TournamentController::class, 'registerParticipant']);
    Route::delete('/participants/{participant}', [TournamentController::class, 'removeParticipant']);

    Route::post('/{tournament}/matches', [TournamentController::class, 'createMatch']);
    Route::post('/matches/{match}/result', [TournamentController::class, 'registerResult']);
});

Route::post('/enviar-correo', [MailController::class, 'enviar']);
Route::post('/resetPassword', [ResetPasswordController::class, 'resetPassword']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Login (public)
Route::post('login', [UserController::class, 'login']);

// Routes protégées par token
Route::middleware(['auth:api'])->group(function () {

    // Route pour récupérer les infos du user connecté
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // Routes réservées aux admins
    Route::middleware(['role:admin'])->group(function () {

        Route::get('/roles', [RoleController::class, 'listRoles']);      // ✅ Liste tous les rôles disponibles
        // CRUD Users
        Route::post('/users', [UserController::class, 'createUser']);
        Route::get('/users', [UserController::class, 'listUsers']);
        Route::put('/users/{id}', [UserController::class, 'updateUser']);
        Route::delete('/users/{id}', [UserController::class, 'deleteUser']);

        // Lister les types spécifiques
        Route::get('/medecins', [UserController::class, 'listMedecins']);
        Route::get('/patients', [UserController::class, 'listPatients']);
        Route::get('/infirmiers', [UserController::class, 'listInfirmiers']);
    });
});

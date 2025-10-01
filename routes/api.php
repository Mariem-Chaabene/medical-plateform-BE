<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post("createUser","UserController@register");
Route::post("login","UserController@login");

// ----------------------
// PROTECTED ROUTES (TOKEN REQUIRED)
// ----------------------
Route::middleware('auth:api')->group(function () {
    // Créer un utilisateur (admin uniquement)
    Route::post('/users', [UserController::class, 'createUser']);

    // Lister tous les utilisateurs (admin uniquement)
    Route::get('/users', [UserController::class, 'listUsers']);
});
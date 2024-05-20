<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/user_event/{id}', [AuthController::class, 'index']);
Route::post('/checktoken', [AuthController::class, 'checkToken']);


Route::post('/create', [EventController::class, 'create']);
Route::middleware('auth:sanctum')->delete('/event_delete/{id}', [EventController::class, 'deleteEvent']);
Route::put('/event_update/{id}', [EventController::class, 'updateEvent']);

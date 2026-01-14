<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TopUpController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//Noran
Route::apiResource('banks', BankController::class);
Route::middleware('auth:sanctum')
    ->post('/top-ups', [TopUpController::class, 'store']);

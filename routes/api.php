<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransfersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\BeneficiaryController;
=======
use App\Http\Controllers\BankController;
use App\Http\Controllers\TopUpController;
>>>>>>> main

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/generate-qr', [TransfersController::class, 'generateQr']);
    Route::post('/transfers-phone', [TransfersController::class, 'phoneTransfers']);
        Route::get('/verify-receiver/{phone}', [TransfersController::class, 'verifyReceiver']);

    Route::post('/transfers-qr', [TransfersController::class, 'QrTransfers']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index']);
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store']);
    Route::delete('/beneficiaries/{beneficiaryId}', [BeneficiaryController::class, 'destroy']);

    });
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//Noran
Route::apiResource('banks', BankController::class);
Route::middleware('auth:sanctum')
    ->post('/top-ups', [TopUpController::class, 'store']);

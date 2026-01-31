<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// User Routes
Route::apiResource('users', UserController::class);

// Account Routes
Route::apiResource('accounts', AccountController::class);
Route::post('accounts/{account}/activate', [AccountController::class, 'activate']);

// Get user accounts
Route::get('users/{user}/accounts', [AccountController::class, 'getUserAccounts']);

// Transfer Routes
Route::prefix('transfers')->group(function () {
    Route::post('/', [TransferController::class, 'transfer']);
    Route::get('/reference/{reference}', [TransferController::class, 'getByReference']);
});

// Transaction History
Route::get('accounts/{accountId}/transactions', [TransferController::class, 'getAccountHistory']);

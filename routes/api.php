<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function (): void {
    Route::post('auth/tokens', [AuthController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::delete('auth/tokens/current', [AuthController::class, 'destroy']);
        Route::get('tickets', [TicketController::class, 'index']);
        Route::post('tickets', [TicketController::class, 'store']);
        Route::get('tickets/{ticket}', [TicketController::class, 'show']);
    });
});

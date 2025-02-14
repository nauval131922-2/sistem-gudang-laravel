<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/user', [UserController::class, 'store'])->middleware(['ableCreateUser']);

    Route::get('/item', [ItemController::class, 'index']);
    Route::get('/item/{id}', [ItemController::class, 'show']);
    Route::post('/item', [ItemController::class, 'store'])->middleware(['ableCreateUpdateItem']);
    Route::patch('/item/{id}', [ItemController::class, 'update'])->middleware(['ableCreateUpdateItem']);
    Route::delete('/item/{id}', [ItemController::class, 'destroy']);

    // loan
    Route::get('/loan', [LoanController::class, 'index']);

    Route::get('/order/{id}/set-as-done', [OrderController::class, 'setAsDone'])->middleware(['ableFinishOrder']);
    Route::get('/order/{id}/payment', [OrderController::class, 'payment'])->middleware(['ablePayOrder']);
    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order', [OrderController::class, 'store'])->middleware(['ableCreateOrder']);

    Route::get('/order-report', [OrderController::class, 'orderReport'])->middleware(['ableSeeOrderReport']);
});

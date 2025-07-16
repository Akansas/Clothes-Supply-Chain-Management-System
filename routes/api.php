<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delivery\Api\FeedbackController;
use App\Http\Controllers\Delivery\Api\MapController;
use App\Http\Controllers\Delivery\Api\TrendsController;

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

Route::middleware('auth:sanctum')->get('/delivery/feedback', [FeedbackController::class, 'index']);
Route::middleware('auth:sanctum')->get('/delivery/map', [MapController::class, 'index']);
Route::middleware('auth:sanctum')->get('/delivery/trends', [TrendsController::class, 'index']);

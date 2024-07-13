<?php

use App\Http\Controllers\api\v1\GameController;
use App\Http\Controllers\api\v1\PlayerController;
use App\Http\Controllers\api\v1\PlayerGameRoundController;
use Illuminate\Http\Request;
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
Route::prefix('v1')->group(function(){
    Route::apiResource('games' , GameController::class)->except('index' ,'destroy','update');
    Route::put('games/{game}/drawing-data' , [GameController::class , 'updateDrawingData']);
    Route::put('games/{game}/start' , [GameController::class , 'startGame']);
    Route::apiResource('games/{game}/players' , PlayerController::class);
    Route::apiResource('games/{game}/players/{player}/game-rounds' , PlayerGameRoundController::class)->only('update');
    Route::get('games/{game}/game-rounds' , [PlayerGameRoundController::class , 'index']);
});


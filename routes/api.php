<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatsController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);



Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/conversations', [ChatsController::class, 'AllChat']);

    Route::post('/conversation', [ChatsController::class, 'MakeChat']);

    Route::post('/message', [ChatsController::class, 'MakeMessage']);

    Route::get('/message/{id}', [ChatsController::class, 'GetAllMessage']);

    Route::get('/getusers', [ChatsController::class, 'GetAllUsers']);
    
});

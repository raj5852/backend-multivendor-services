<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SupportBoxController;
use Illuminate\Support\Facades\Route;



//register
Route::post('register', [AuthController::class, 'Register']);
//login
Route::post('login', [AuthController::class, 'Login']);

Route::post('logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('supportbox', SupportBoxController::class);
    Route::post('ticket-review', [SupportBoxController::class,'review']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

});


Route::get('test', function (Request $request) {
});

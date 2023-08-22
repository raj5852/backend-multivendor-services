<?php

use App\Http\Controllers\API\User\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function(){

    Route::post('/contact-store', [ContactController::class, 'store']);

});


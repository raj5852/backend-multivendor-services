<?php

use App\Http\Controllers\API\User\ContactController;
use App\Http\Controllers\API\User\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'isUser', 'userOnline'])->prefix('user')->group(function () {
    Route::get('user', function () {
        return auth()->user();
    });
});

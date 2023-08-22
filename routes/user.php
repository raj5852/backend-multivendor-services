<?php

use App\Http\Controllers\API\User\ContactController;
use App\Http\Controllers\API\User\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function(){

    Route::post('/contact-store', [ContactController::class, 'store']);

    // getting settings infos
    Route::get('/settings', [SettingsController::class, 'index']);
    // getting companion infos
    Route::get('/companions', [SettingsController::class, 'companion']);
    // getting Faq infos
    Route::get('/faqs', [SettingsController::class, 'faq']);
    // getting fottermedia infos
    Route::get('/fottermedias', [SettingsController::class, 'fottermedia']);
    // getting members infos
    Route::get('/members', [SettingsController::class, 'members']);
    // getting mission infos
    Route::get('/missions', [SettingsController::class, 'mission']);
    // getting org-one infos
    Route::get('/org-one', [SettingsController::class, 'orgOne']);
    // getting org-two infos
    Route::get('/org-two', [SettingsController::class, 'orgTwo']);
    // getting service infos
    Route::get('/services', [SettingsController::class, 'service']);
    // getting partner infos
    Route::get('/partners', [SettingsController::class, 'partner']);
    // getting partner infos
    Route::get('/testimonials', [SettingsController::class, 'testimonial']);

    
});


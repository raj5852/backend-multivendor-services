<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\SupportBoxController;

use App\Http\Controllers\API\User\ContactController;
use App\Http\Controllers\API\User\SettingsController;

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

Route::post('/contact-store', [ContactController::class, 'store']);

// getting settings infos
Route::get('/settings', [SettingsController::class, 'index']);
// getting companion infos
Route::get('/companions', [SettingsController::class, 'companion']);
// getting Faq infos
Route::get('/faqs', [SettingsController::class, 'faq']);
// getting fottermedia infos
Route::get('/footer-medias', [SettingsController::class, 'fottermedia']);
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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

});


Route::get('test', function (Request $request) {
});

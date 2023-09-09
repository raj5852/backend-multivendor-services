<?php

use App\Http\Controllers\AamarpayController;
use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\API\Admin\AdminAdvertiseController;
use App\Http\Controllers\API\Admin\SupportBoxCategoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ServiceOrderController;
use App\Http\Controllers\API\SupportBoxController;

use App\Http\Controllers\API\User\ContactController;
use App\Http\Controllers\API\User\SettingsController;
use App\Http\Controllers\BuySubscription;
use App\Http\Controllers\CouponUsedController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;



//register
Route::post('register', [AuthController::class, 'Register']);
//login
Route::post('login', [AuthController::class, 'Login']);

Route::post('logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {

    Route::resource('supportbox', SupportBoxController::class);
    Route::post('ticket-review', [SupportBoxController::class, 'review']);

    Route::post('ticket-replay', [SupportBoxController::class, 'supportreplay']);

    Route::apiResource('service/order', ServiceOrderController::class);
    Route::post('service/order/status', [ServiceOrderController::class,'status']);
    // Route::apiResource('coupon-apply', CouponUsedController::class);
    Route::get('all-ticket-category',[SupportBoxCategoryController::class,'index']);
    Route::get('ticket-category-to-problem/{id}',[SupportBoxCategoryController::class,'ticketcategorytoproblem']);

    Route::get('buy/subscription/{id}',[BuySubscription::class,'buy']);
    Route::post('apply/coupon',[BuySubscription::class,'coupon']);
    Route::post('buy-subscription',[BuySubscription::class,'buysubscription']);

    Route::post('create-advertise', [AdminAdvertiseController ::class,'store']);
    Route::get('all-advertise', [AdvertiseController ::class,'index']);
    Route::get('advertise/{id}', [AdvertiseController ::class,'show']);

});

Route::prefix('aaparpay')->group(function () {
    Route::post('success', [AamarpayController::class, 'success']);
    Route::post('fail', [AamarpayController::class, 'fail']);
    Route::post('cancel', [AamarpayController::class, 'cancel']);
});

Route::post('/contact-store', [ContactController::class, 'store']);


Route::get('/settings', [SettingsController::class, 'index']);

Route::get('/companions', [SettingsController::class, 'companion']);

Route::get('/faqs', [SettingsController::class, 'faq']);

Route::get('/footer-medias', [SettingsController::class, 'fottermedia']);

Route::get('/members', [SettingsController::class, 'members']);

Route::get('/missions', [SettingsController::class, 'mission']);

Route::get('/org-one', [SettingsController::class, 'orgOne']);

Route::get('/org-two', [SettingsController::class, 'orgTwo']);

Route::get('/services', [SettingsController::class, 'service']);

Route::get('/it-services', [SettingsController::class, 'Itservice']);

Route::get('/partners', [SettingsController::class, 'partner']);

Route::get('/testimonials', [SettingsController::class, 'testimonial']);

Route::get('/subscriptions', [SubscriptionController::class, 'index']);




Route::middleware('auth:sanctum')->get('/user', function () {
    return auth()->user();
});

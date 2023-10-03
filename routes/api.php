<?php

use App\Http\Controllers\AamarpayController;
use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\API\Admin\AdminAdvertiseController;
use App\Http\Controllers\API\Admin\SupportBoxCategoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CouponListController;
use App\Http\Controllers\API\HistoryController;
use App\Http\Controllers\API\RechargeController;
use App\Http\Controllers\API\ServiceOrderController;
use App\Http\Controllers\API\SupportBoxController;

use App\Http\Controllers\API\User\ContactController;
use App\Http\Controllers\API\User\SettingsController;
use App\Http\Controllers\BuySubscription;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Vendor\VendorServiceController;
use App\Http\Controllers\API\Vendor\OrderDeliveryController;
use App\Http\Controllers\DollerRateController;
use App\Http\Controllers\RenewController;

//register
Route::post('register', [AuthController::class, 'Register']);
//login
Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {



    Route::resource('main-services',VendorServiceController::class);
    Route::get('service/orders',[VendorServiceController::class,'serviceorders']);
    Route::post('service/status',[VendorServiceController::class,'statusChange']);

    Route::get('service/myorders/{id}',[VendorServiceController::class,'singlemyorder']);

    Route::get('service/orders/view/{id}',[VendorServiceController::class,'ordersview']);

    // Route::post('service/delivery-to-customer',[VendorServiceController::class,'deliverytocustomer']);
    Route::resource('service/delivery-to-customer',OrderDeliveryController::class);
    Route::get('service-category-subcategory',[VendorServiceController::class,'categorysubcategory']);


    Route::resource('supportbox', SupportBoxController::class);
    Route::post('ticket-review', [SupportBoxController::class, 'review']);

    Route::post('ticket-replay', [SupportBoxController::class, 'supportreplay']);

    Route::apiResource('service/order', ServiceOrderController::class);
    Route::post('service/order/status', [ServiceOrderController::class,'status']);
    // Route::apiResource('coupon-list', CouponUsedController::class);
    Route::get('all-ticket-category',[SupportBoxCategoryController::class,'index']);
    Route::get('ticket-category-to-problem/{id}',[SupportBoxCategoryController::class,'ticketcategorytoproblem']);

    Route::get('buy/subscription/{id}',[BuySubscription::class,'buy']);
    Route::post('apply/coupon',[BuySubscription::class,'coupon']);
    Route::post('buy-subscription',[BuySubscription::class,'buysubscription']);

    Route::post('create-advertise', [AdminAdvertiseController ::class,'store']);
    // advertise-success

    Route::get('all-advertise', [AdvertiseController ::class,'index']);
    Route::get('advertise/{id}', [AdvertiseController ::class,'show']);

    Route::get('coupon-lists',[CouponListController::class,'index']);
    Route::post('renew-subscription',[RenewController::class,'store']);

    Route::post('recharge',[RechargeController::class,'recharge']);
    Route::get('transition-history',[HistoryController::class,'index']);
});

Route::prefix('aaparpay')->group(function () {

    Route::post('advertise-success',[AamarpayController::class, 'advertisesuccess']);
    Route::post('service-success',[AamarpayController::class, 'servicesuccess']);
    Route::post('renew-success',[AamarpayController::class, 'renewsuccess']);
    Route::post('recharge-success',[AamarpayController::class, 'rechargesuccess']);

    Route::post('subscription-success', [AamarpayController::class, 'subscriptionsuccess']);


    Route::post('fail', [AamarpayController::class, 'fail']);
    Route::post('cancel', [AamarpayController::class, 'cancel']);
});

Route::post('/contact-store', [ContactController::class, 'store']);
Route::get('all-services',[VendorServiceController::class,'allservice']);
Route::get('services-view/{id}',[VendorServiceController::class,'serviceshow']);

Route::get('doller-rate',[DollerRateController::class,'index']);
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

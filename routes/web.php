<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SupportBox;
use App\Models\User;
use App\Models\Withdraw;
use App\Services\SubscriptionDueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return "API for SOS projec :)";
});

// Auth::routes();


Route::get('migrate', function () {
    Artisan::call('migrate');
});

Route::get('rollback', function () {
    Artisan::call('migrate:rollback', [
        '--step' => 1,
    ]);
});

Route::get('seed', function () {
    Artisan::call('db:seed');
});




Route::get('demo', function () {

    return   Coupon::query()
    ->where(['name'=> 'coupon','status'=>'active'])
    ->whereDate('expire_date', '>', now())
    ->withCount('couponused')
    ->having('limitation', '>', \DB::raw('couponused_count'))
    ->exists();


    // return Coupon::query()
    // ->where('name', 'vendor0511')
    // ->whereDate('expire_date', '>', now())
    // ->withCount('couponused')
    // ->having('limitation', '>', \DB::raw('couponused_count'))
    // ->exists();


});

<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\CancelOrderBalance;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsed;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\Subscription;
use App\Models\SupportBox;
use App\Models\User;
use App\Models\VendorService;
use App\Models\Withdraw;
use App\Services\SubscriptionDueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
    $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
    $currentMonthStart = Carbon::now()->startOfMonth();
    $currentMonthEnd = Carbon::now()->endOfMonth();

    return   Category::query()
        ->withCount([
            'products as product_qty' => function ($query) {
                $query->where('status', 'active');
            },
            'order as total_qty_last_month' => function ($query) use ($lastMonthStart, $lastMonthEnd) {
                $query->select(DB::raw('sum(qty)'))
                    ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                    ->where('status', 'delivered');
            },
            'order as total_qty_current_month' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
                $query->select(DB::raw('sum(qty)'))
                    ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                    ->where('status', 'delivered');
            },
            'order as sold_qty' => function ($query) {
                $query->select(DB::raw('sum(qty)'))
                    ->where('status', 'delivered');
            }
        ])
        ->orderByDesc('sold_qty')
        ->take(10)
        ->get();
});

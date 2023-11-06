<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\SupportBox;
use App\Models\User;
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

  return  $product = Product::query()
    ->with(['category', 'subcategory', 'productImage', 'brand', 'vendor:id,name,image', 'productdetails' => function ($query) {
        $query->where(['user_id' => auth()->id(), 'status' => 3]);
    }])
    ->where('status', 'active')
    ->withAvg('productrating', 'rating')
    ->with('productrating.affiliate:id,name,image')
    ->withwhereHas('vendor', function ($query) {
        $query->withCount(['vendoractiveproduct' => function ($query) {
            $query->where('status', 1);
        }])
            ->withwhereHas('usersubscription', function ($query) {

                $query->where(function ($query) {
                    $query->whereHas('subscription', function ($query) {
                        $query->where('plan_type', 'freemium');
                    })
                        ->where('expire_date', '>', now());
                })
                    ->orwhere(function ($query) {
                        $query->whereHas('subscription', function ($query) {
                            $query->where('plan_type', '!=', 'freemium');
                        })
                            ->where('expire_date', '>', now()->subMonth(1));
                    });
            })
            ->withSum('usersubscription', 'affiliate_request')
            ->having('vendoractiveproduct_count', '<', \DB::raw('usersubscription_sum_affiliate_request'));
    })
    ->find(66);

});

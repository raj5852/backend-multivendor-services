<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\SupportBox;
use App\Models\User;
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

Auth::routes();


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
   $datas  = array (
        0 =>
        array (
          'id' => 1,
          'name' => 'Bruno',
          'phone' => 207,
          'email' => 'wowilag@mailinator.com',
          'city' => 'Magnam ex perspiciat',
          'address' => 'Excepteur aliquid qu',
          'vendor_id' => '2',
          'product_id' => '37',
          'variants' =>
          array (
            0 =>
            array (
              'id' => 99,
              'qty' => '1',
              'size' => NULL,
              'color' => NULL,
              'variant_id' => NULL,
            ),
          ),
          'cart_id' => 94,
          'amount' => '23',
        ),
        1 =>
        array (
          'id' => 2,
          'name' => 'Imelda',
          'phone' => 123,
          'email' => 'dacys@mailinator.com',
          'city' => 'Eveniet alias solut',
          'address' => 'In qui quo omnis par',
          'vendor_id' => '2',
          'product_id' => '37',
          'cart_id' => 94,
          'amount' => '23',
          'variants' =>
          array (
            0 =>
            array (
              'id' => 99,
              'qty' => '1',
              'size' => NULL,
              'color' => NULL,
              'variant_id' => NULL,
            ),
          ),
        ),
    );

    foreach($datas as $data){
      $totalqty =  collect($data['variants'])->sum('qty');
    }

    //  $mydatas =  $datas['datas'];
    // foreach($mydatas as $dt){
    //     echo collect($dt['variants'])->sum('qty');
    // }
});


// Route::post('test', [TestController::class, 'index'])->name('test');

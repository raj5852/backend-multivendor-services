<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\Product;
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

  return array (
    'product_id' => 23,
    'cartItems' =>
    array (
      0 =>
      array (
        'id' => '1',
        'qty' => '2',
        'size' => 'M',
        'color' => 'BLACK',
      ),
      1 =>
      array (
        'id' => '2',
        'qty' => '3',
        'size' => 'S',
        'color' => 'BLUE',
      ),
    ),
    'vendor_id' => '97',
    'product_price' => '100',
    'discount_type' => 'flat',
    'discount_rate' => '20',
    'category_id' => 1,
    'purchase_type' => 'bulk',
);
//   [2023-10-19 11:21:38] local.INFO: array (
//     'product_id' => 23,
//     'cartItems' =>
//     array (
//       0 =>
//       array (
//         'id' => '1',
//         'qty' => '2',
//         'size' => 'M',
//         'color' => 'BLACK',
//       ),
//       1 =>
//       array (
//         'id' => '2',
//         'qty' => '3',
//         'size' => 'S',
//         'color' => 'BLUE',
//       ),
//     ),
//     'vendor_id' => '97',
//     'product_price' => '100',
//     'discount_type' => 'flat',
//     'discount_rate' => '20',
//     'category_id' => 1,
//     'purchase_type' => 'single',
//   ) ;
});


Route::get('test', function () {
    return view('test');
});
Route::post('test', [TestController::class, 'index'])->name('test');

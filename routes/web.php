<?php

use App\Enums\Status;
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


Route::get('seed', function () {
    Artisan::call('db:seed');
});



Route::get('demo', function () {
    return array (
        'datas' =>
        array (
          0 =>
          array (
            'id' => 1,
            'name' => 'raj',
            'phone' => 2121212121212,
            'email' => 'raj.web58@gmail.com',
            'city' => 'Dhaka',
            'address' => 'sa',
            'vendor_id' => '2',
            'product_id' => '5',
            'cart_id' => 5,
            'amount' => '1',
            'variants' =>
            array (
              0 =>
              array (
                'id' => 4,
                'qty' => '1',
                'size' => NULL,
                'color' => NULL,
                'variant_id' => NULL,
              ),
            ),
          ),
          1 =>
          array (
            'id' => 2,
            'name' => 'raj',
            'phone' => 2121212121212,
            'email' => 'raj.web58@gmail.com',
            'city' => 'Dhaka',
            'address' => 'sas',
            'vendor_id' => '2',
            'product_id' => '5',
            'cart_id' => 5,
            'amount' => '1',
            'variants' =>
            array (
              0 =>
              array (
                'id' => 4,
                'qty' => '1',
                'size' => NULL,
                'color' => NULL,
                'variant_id' => NULL,
              ),
            ),
          ),
        ),
      ) ;

return array (
    'datas' =>
    array (
      0 =>
      array (
        'id' => 1,
        'name' => 'raj',
        'phone' => 2121212121212,
        'email' => 'raj.web58@gmail.com',
        'city' => 'Dhaka',
        'address' => 'sd',
        'vendor_id' => '2',
        'product_id' => '2',
        'variants' =>
        array (
          0 =>
          array (
            'id' => 3,
            'qty' => '1',
            'size' => 'xl',
            'color' => 'dsdsds',
            'variant_id' => '1',
          ),
        ),
        'cart_id' => 4,
        'amount' => '1',
      ),
      1 =>
      array (
        'id' => 2,
        'name' => 'raj',
        'phone' => 2121212121212,
        'email' => 'raj.web58@gmail.com',
        'city' => 'Dhaka',
        'address' => 'fsd',
        'vendor_id' => '2',
        'product_id' => '2',
        'cart_id' => 4,
        'amount' => '1',
        'variants' =>
        array (
          0 =>
          array (
            'id' => 3,
            'qty' => '1',
            'size' => 'xl',
            'color' => 'dsdsds',
            'variant_id' => '1',
          ),
        ),
      ),
    ),
  );

});

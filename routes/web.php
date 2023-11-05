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

    $datas =  array(
        'datas' =>
        array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Amaya',
                'phone' => 752,
                'email' => 'lesema@mailinator.com',
                'city' => 'Qui ut et aliquid ex',
                'address' => 'Obcaecati autem moll',
                'vendor_id' => '2',
                'product_id' => '37',
                'variants' =>
                array(
                    0 =>
                    array(
                        'id' => 98,
                        'qty' => '2',
                        'size' => NULL,
                        'color' => NULL,
                        'variant_id' => NULL,
                    ),
                ),
                'cart_id' => 93,
                'amount' => '23',
            ),
        ),
        'cart_id' => 93,
        'payment_type' => 'my-wallet',
    );

     $mydatas =  $datas['datas'];
    foreach($mydatas as $dt){
        echo collect($dt['variants'])->sum('qty');
    }
});


// Route::post('test', [TestController::class, 'index'])->name('test');

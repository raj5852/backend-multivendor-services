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
    $data =  [
        [
            "min_bulk_qty" => 5,
            "min_bulk_price" => 5,
            "bulk_commission" => 5,
            "advance_payment" => 10,
        ],
        [
            "min_bulk_qty" => 10,
            "min_bulk_price" => 5,
            "bulk_commission" => 3,
            "advance_payment" => 3
        ],
        [
            "min_bulk_qty" => 30,
            "min_bulk_price" => 5,
            "bulk_commission" => 3,
            "advance_payment" => 3
        ],

        [
            "min_bulk_qty" => 52,
            "min_bulk_price" => 5,
            "bulk_commission" => 3,
            "advance_payment" => 3
        ]
    ];
    return collect($data)->where('min_bulk_qty','<=',51)->max();
});


Route::get('test', function () {
    return view('test');
});
Route::post('test', [TestController::class, 'index'])->name('test');

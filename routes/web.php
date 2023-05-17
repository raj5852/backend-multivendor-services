<?php

use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


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

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('categories', [CategoryController::class, 'index'])->name('admin.categories');
// Route::get('category/add', [CategoryController::class, 'add'])->name('admin.category.add');
// Route::post('category/store', [CategoryController::class, 'Store'])->name('admin.category.store');
// Route::get('category/edit/{id}', [CategoryController::class, 'Edit'])->name('admin.category.edit');
// Route::post('category/update/{id}', [CategoryController::class, 'Update'])->name('admin.category.update');
// Route::get('category/delete/{id}', [CategoryController::class, 'Delete'])->name('admin.category.delete');
Route::get('test', [ApiTestController::class, 'index']);

Route::get('migrate', function () {
    Artisan::call('migrate');
});




Route::get('demo', function () {
    // $json_data = '[{ "name": "Yellow", "itemID": 11, "id": 2, "qty": "2321" }, { "name": "Yellow", "itemID": 12, "id": 2, "qty": null }, { "name": "Yellow", "itemID": 13, "id": 2, "qty": "2321" }]';

    // $php_array = json_decode($json_data, true);
    // return $php_array;
    // // print_r($php_array);


    // return orderId();

    // [
    //     ['qty'=>1],
    //     ['qty'=>10],
    //     ['qty'=>null]
    // ]
    // vendor:
    // vendor/profile //get
    // vendor/update/profile //post  parameters{name,number,image} image optional

    // admin:
    // admin/profile //get
    // admin/update/profile //post parameters{name,number,image} image optional

    // affiliator:
    // affiliator/profile //get
    // affiliator/update/profile //post parameters{name,number,image} image optional
});

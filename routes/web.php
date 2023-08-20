<?php

use App\Http\Controllers\ApiTestController;
use Illuminate\Support\Facades\Artisan;
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
    // return bcrypt('password');
    $data = '[
        {
        "id": 1,
        "migration": "2014_10_12_000000_create_users_table",
        "batch": 1
        },
        {
        "id": 2,
        "migration": "2014_10_12_100000_create_password_resets_table",
        "batch": 1
        },
        {
        "id": 3,
        "migration": "2019_08_19_000000_create_failed_jobs_table",
        "batch": 1
        },
        {
        "id": 4,
        "migration": "2019_12_14_000001_create_personal_access_tokens_table",
        "batch": 1
        },
        {
        "id": 5,
        "migration": "2023_01_28_174300_create_categories_table",
        "batch": 1
        },
        {
        "id": 6,
        "migration": "2023_02_01_111724_create_brands_table",
        "batch": 1
        },
        {
        "id": 7,
        "migration": "2023_02_18_051712_create_subcategories_table",
        "batch": 1
        },
        {
        "id": 8,
        "migration": "2023_02_18_051747_create_products_table",
        "batch": 1
        },
        {
        "id": 9,
        "migration": "2023_02_28_052500_create_product_images_table",
        "batch": 1
        },
        {
        "id": 10,
        "migration": "2023_03_05_053821_create_colors_table",
        "batch": 1
        },
        {
        "id": 11,
        "migration": "2023_03_05_053934_create_sizes_table",
        "batch": 1
        },
        {
        "id": 12,
        "migration": "2023_03_05_071119_create_recharges_table",
        "batch": 1
        },
        {
        "id": 13,
        "migration": "2023_03_05_110402_create_carts_table",
        "batch": 1
        },
        {
        "id": 14,
        "migration": "2023_03_18_041024_create_product_details_table",
        "batch": 1
        },
        {
        "id": 15,
        "migration": "2023_05_03_182602_create_specifications_table",
        "batch": 1
        },
        {
        "id": 16,
        "migration": "2023_05_06_001803_create_cart_details_table",
        "batch": 1
        },
        {
        "id": 17,
        "migration": "2023_05_07_052918_create_orders_table",
        "batch": 1
        },
        {
        "id": 18,
        "migration": "2023_05_08_080501_create_order_variants_table",
        "batch": 1
        },
        {
        "id": 19,
        "migration": "2023_05_14_082349_create_pending_balances_table",
        "batch": 1
        },
        {
        "id": 20,
        "migration": "2023_05_15_061621_create_banks_table",
        "batch": 1
        },
        {
        "id": 21,
        "migration": "2023_05_15_104339_create_vendor_payment_requests_table",
        "batch": 1
        },
        {
        "id": 22,
        "migration": "2023_05_16_063837_create_withdraws_table",
        "batch": 1
        },
        {
        "id": 23,
        "migration": "2023_08_19_114259_create_coupons_table",
        "batch": 1
        }
        ]';

       $migrations = json_decode($data);
        foreach($migrations as $migration){
            DB::table('migrations')->insert([
                'id'=>$migration->id,
                'migration'=>$migration->migration,
                'batch'=>$migration->batch,
            ]);
        }

});


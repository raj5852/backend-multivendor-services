<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
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

   return $vendor = User::where('role_as', '!=', '1')
            // ->when($status == 'active', function ($q) {
            //     return $q->where('status', 'active');
            // })
            // ->when($status == 'pending', function ($q) {
            //     return $q->where('status', 'pending');
            // })

            // ->when(request()->enum()('type',['vendor','aff']), function ($q) {
            //     // return $q->where('role_as', '2');
            //     dd(1);
            // })
            // ->when(request('type') == 'affiliate' , function ($q) {
            //     return $q->where('role_as', '3');
            // })
            // ->when(request('type') == 'affiliate' , function ($q) {
            //     return $q->where('role_as', '3');
            // })
            ->when(
                request('email'),
                fn ($q, $email) => $q->where('email', 'like', "%{$email}%")
                    ->orWhere('id', 'like', "%{$email}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

});


// Route::post('test', [TestController::class, 'index'])->name('test');

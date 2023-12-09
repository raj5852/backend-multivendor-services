<?php

use App\Enums\Status;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\CancelOrderBalance;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsed;
use App\Models\Order;
use App\Models\Product;
use App\Models\RolePermission;
use App\Models\ServiceOrder;
use App\Models\Subscription;
use App\Models\SupportBox;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\VendorService;
use App\Models\Withdraw;
use App\Services\SubscriptionDueService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

Route::get('role-permission', function () {
    $user = User::find(1);
    $role = Role::first();

    $permissions = Permission::pluck('id', 'id')->all();

    $role->syncPermissions($permissions);

    $user->assignRole([$role->id]);
});


// Route::get('demo',[DashboardController::class,'index']);
Route::get('demo', function () {

    //  return   UserSubscription::query()
    //     ->where('subscription_price', 0)
    //     ->whereDate('expire_date', '<', now()->addDay(12))
    //     ->first()
    //     ->update([
    //         'expire_date'=>now()->addDay(2)
    //     ]);

    // $permission =  Permission::where('name', 'add-affiliate')->first();
    // $rolepermission = RolePermission::where('permission_id', $permission->id)->first();
    // $userrole = DB::table('model_has_roles')->where('model_id', auth()->id())->first();
    // if ($userrole->role_id == $rolepermission->role_id) {
    //     return 1;
    // }
    // return false;
    // User


});

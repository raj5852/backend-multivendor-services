<?php

use App\Http\Controllers\API\Admin\BankController;
use App\Http\Controllers\API\Admin\ColorController as AdminColorController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Admin\PaymentHistoryController;
use App\Http\Controllers\API\Admin\SizeController as AdminSizeController;
use App\Http\Controllers\API\Admin\WithdrawController as AdminWithdrawController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\ColorController;
use App\Http\Controllers\API\SizeController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AffiliatorAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\Vendor\VendorController;
use App\Http\Controllers\API\Affiliate\AffiliateController;
use App\Http\Controllers\API\Affiliate\BalanceController;
use App\Http\Controllers\API\Affiliate\BankController as AffiliateBankController;
use App\Http\Controllers\API\Affiliate\CartController;
use App\Http\Controllers\API\Affiliate\CheckoutController;
use App\Http\Controllers\API\Affiliate\OrderController;
use App\Http\Controllers\API\Affiliate\PendingBalanceController;
use App\Http\Controllers\API\Affiliate\WithdrawController;
use App\Http\Controllers\API\Vendor\BankController as VendorBankController;
use App\Http\Controllers\API\Vendor\BrandController as VendorBrandController;
use App\Http\Controllers\API\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\API\Vendor\PaymentRequestController;
use App\Http\Controllers\API\Vendor\RequestProductController;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.product_id These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('demo',function(){
//     return User::query()->latest()->get();
//     // $user =  User::find(24);
//     // $user->status = 'active';
//     // $user->save();
// });


//register
Route::post('register', [AuthController::class, 'Register']);
//login
Route::post('login', [AuthController::class, 'Login']);

Route::post('logout', [AuthController::class, 'logout']);


// vendor

Route::middleware(['auth:sanctum','isAPIVendor'])->group(function () {
    Route::get('/checkingAuthenticatedVendor', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });

    Route::get('vendor/profile', [VendorController::class, 'VendorProfile']);
    Route::post('vendor/update/profile', [VendorController::class, 'VendorUpdateProfile']);

    //vendor product
    Route::get('vendor/product/{status?}', [VendorController::class, 'VendorProduct']);
    Route::post('vendor-store-product', [VendorController::class, 'VendorProductStore']);
    Route::get('vendor-edit-product/{id}', [VendorController::class, 'VendorProductEdit']);
    Route::post('vendor-update-product/{id}', [VendorController::class, 'VendotUpdateProduct']);
    Route::delete('vendor-delete-product/{id}', [VendorController::class, 'VendorDelete']);
    Route::delete('vendor-delete-image/{id}', [VendorController::class, 'VendorDeleteImage']);


    Route::get('vendor-all-category', [VendorController::class, 'AllCategory']);
    Route::get('vendor-all-subcategory', [VendorController::class, 'AllSubCategory']);
    // Route::get('vendor-all/brand', [VendorController::class, 'AllBrand']);
    Route::get('vendor-all-color', [VendorController::class, 'AllColor']);
    Route::get('vendor-all-size', [VendorController::class, 'AllSize']);

    //brand create
    Route::post('vendor-brand-create',[VendorBrandController::class,'create']);
    Route::get('vendor-brands',[VendorBrandController::class,'allBrand']);
    Route::get('vendor-brands/active',[VendorBrandController::class,'allBrandActive']);


    Route::delete('vendor-brand-delete/{id}',[VendorBrandController::class,'delete']);
    Route::get('vendor-brand-edit/{id}',[VendorBrandController::class,'edit']);
    Route::post('vendor-brand-update/{id}',[VendorBrandController::class,'update']);

    Route::get('vendor/balabrandnce/request', [VendorController::class, 'VendorBalanceRequest']);
    Route::post('vendor/request/sent', [VendorController::class, 'VendorRequestSent']);

    Route::get('vendor-product-approval/{id}', [VendorController::class, 'approval']);
    Route::get('vendor-product-reject/{id}', [VendorController::class, 'reject']);
    Route::get('vendor-all/product-accepted/{id}', [VendorController::class, 'Accepted']);


    //color
    Route::post('store-color', [ColorController::class, 'Colortore']);
    Route::get('view-color/{status?}', [ColorController::class, 'ColorIndex']);
    Route::get('edit-color/{id}', [ColorController::class, 'ColorEdit']);
    Route::post('update-color/{id}', [ColorController::class, 'ColorUpdate']);
    Route::delete('delete-color/{id}', [ColorController::class, 'destroy']);


    //size route
    Route::post('store-size', [SizeController::class, 'Sizestore']);
    Route::get('view-size/{status?}', [SizeController::class, 'SizeIndex']);
    Route::get('edit-size/{id}', [SizeController::class, 'SizeEdit']);
    Route::put('update-size/{id}', [SizeController::class, 'SizeUpdate']);
    Route::delete('delete-size/{id}', [SizeController::class, 'destroy']);

    //all categories
    Route::get('vendor-categories',[ProductController::class,'AllCategory']);
    Route::get('vendor-category-subcategory/{id}',[ProductController::class,'catecoryToSubcategory']);

    // all sub categories
    Route::get('vendor-subcategories',[SubCategoryController::class,'SubCategoryIndex']);


    //affi request products
    Route::get('affiliator/request/product/pending', [RequestProductController::class, 'RequestPending']);
    Route::get('affiliator/request/product/active', [RequestProductController::class, 'RequestActive']);
    Route::get('affiliator/request/product/all', [RequestProductController::class, 'RequestAll']);
    Route::get('affiliator/request/product/rejected', [RequestProductController::class, 'RequestRejected']);
    Route::get('affiliator/request/product/view/{id}',[RequestProductController::class,'RequestView']);
    Route::post('affiliator/product-update/{id}', [RequestProductController::class, 'RequestUpdate']);

    //
    //afi orders api
    Route::get('vendor/all-orders',[VendorOrderController::class,'AllOrders']);
    Route::get('vendor/pending-orders',[VendorOrderController::class,'pendingOrders']);
    Route::get('vendor/progress-orders',[VendorOrderController::class,'ProgressOrders']);
    Route::get('vendor/delivered-orders',[VendorOrderController::class,'DeliveredOrders']);
    Route::get('vendor/cancel-orders',[VendorOrderController::class,'CanceldOrders']);
    Route::get('vendor/hold-orders',[VendorOrderController::class,'HoldOrders']);

    Route::post('vendor/order/update/{id}',[VendorOrderController::class,'updateStatus']);
    Route::get('vendor/order/view/{id}',[VendorOrderController::class,'orderView']);

    //bank show
    Route::get('vendor/banks',[VendorBankController::class,'index']);

    //vendor payment request
    Route::post('vendor/payment/submit',[PaymentRequestController::class,'store']);
    Route::get('vendor/payment/history/{status?}',[PaymentRequestController::class,'history']);
});



//admin route
Route::middleware(['auth:sanctum', 'isAPIAdmin','userOnline'])->group(function () {

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });
    Route::get('admin/profile', [AdminController::class, 'AdminProfile']);
    Route::post('admin/update/profile', [AdminController::class, 'AdminUpdateProfile']);

    Route::get('admin/request/product/pending', [AdminController::class, 'AdminRequestPending']);
    Route::get('admin/request/product/active', [AdminController::class, 'AdminRequestActive']);
    Route::get('admin/request/product/all', [AdminController::class, 'AdminRequestAll']);

    Route::get('admin/request/product/rejected', [AdminController::class, 'RequestRejected']);
    // Route::get('admin/request/product/view/{id}',[AdminController::class,'RequestView']);
    Route::post('admin/request/product-update/{id}', [AdminController::class, 'RequestUpdate']);


    Route::get('admin/request/product/view/{id}', [AdminController::class, 'AdminRequestView']);


    Route::get('product-approval/{id}', [ProductController::class, 'approval']);
    Route::get('product-reject/{id}', [ProductController::class, 'reject']);
    Route::get('all/product-accepted/{id}', [ProductController::class, 'Accepted']);
    Route::get('admin/request/balances', [AdminController::class, 'AdminRequestBalances']);
    Route::get('admin/request/balance/active', [AdminController::class, 'AdminRequestBalanceActive']);



    Route::post('store-category', [CategoryController::class, 'CategoryStore']);
    Route::get('view-category', [CategoryController::class, 'CategoryIndex']);
    Route::get('edit-category/{id}', [CategoryController::class, 'CategoryEdit']);
    Route::post('update-category/{id}', [CategoryController::class, 'UpdateCategory']);
    Route::delete('delete-category/{id}', [CategoryController::class, 'destroy']);

    Route::post('category-status/{id}',[CategoryController::class,'status']);

    // Route::get('all/category', [CategoryController::class, 'AllCategory']);


    //subcategory route
    Route::post('store-subcategory', [SubCategoryController::class, 'SubCategoryStore']);
    Route::get('view-subcategory', [SubCategoryController::class, 'SubCategoryIndex']);
    Route::get('edit-subcategory/{id}', [SubCategoryController::class, 'SubCategoryEdit']);
    Route::post('update-subcategory/{id}', [SubCategoryController::class, 'UpdateSubCategory']);
    Route::delete('delete-subcategory/{id}', [SubCategoryController::class, 'destroy']);

    Route::post('subcategory-status/{id}',[SubCategoryController::class,'status']);


    Route::post('store-brand', [BrandController::class, 'BrandStore']);
    Route::get('view-brand', [BrandController::class, 'BrandIndex']);
    Route::get('view-brand/active', [BrandController::class, 'BrandActive']);
    Route::get('edit-brand/{id}', [BrandController::class, 'BrandEdit']);
    Route::post('update-brand/{id}', [BrandController::class, 'BrandUpdate']);
    Route::delete('delete-brand/{id}', [BrandController::class, 'destroy']);





    Route::get('all-category', [ProductController::class, 'AllCategory']);
    Route::get('all/brand', [ProductController::class, 'AllBrand']);

    // Route::post('store-product', [ProductController::class, 'ProductStore']);
    Route::get('view-product/{status?}', [ProductController::class, 'ProductIndex']);
    Route::get('edit-product/{id}', [ProductController::class, 'ProductEdit']);
    Route::put('update-product/{id}', [ProductController::class, 'UpdateProduct']);
    Route::delete('delete-product/{id}', [ProductController::class, 'destroy']);
    Route::post('admin-product-status-update/{id}',[ProductController::class,'updateStatus']);


    // vendor
    Route::get('vendor/view/{name?}', [UserController::class, 'VendorView']);
    Route::post('vendor/store', [UserController::class, 'VendorStore']);
    Route::get('edit-vendor/{id}', [UserController::class, 'VendorEdit']);
    Route::post('update-vendor/{id}', [UserController::class, 'UpdateVendor']);
    Route::delete('delete-vendor/{id}', [UserController::class, 'VendorDelete']);


    //affiliator
    Route::post('affiliator/store', [UserController::class, 'AffiliatorStore']);
    Route::get('affiliator/view/{name?}', [UserController::class, 'AffiliatorView']);
    Route::get('edit-affiliator/{id}', [UserController::class, 'AffiliatorEdit']);
    Route::post('update-affiliator/{id}', [UserController::class, 'UpdateAffiliator']);
    Route::delete('delete-affiliator/{id}', [UserController::class, 'AffiliatorDelete']);



    Route::post('user/status/update/{id}',[UserController::class,'updateStatus']);

    //colors
    // Route::apiResource('admin-colors',AdminColorController::class);

    //size
    // Route::apiResource('admin-size',AdminSizeController::class);

    Route::get('admin/all-orders',[AdminOrderController::class,'allOrders']);
    Route::get('admin/pending-orders',[AdminOrderController::class,'pendingOrders']);
    Route::get('admin/progress-orders',[AdminOrderController::class,'ProgressOrders']);
    Route::get('admin/delivered-orders',[AdminOrderController::class,'DeliveredOrders']);
    Route::get('admin/cancel-orders',[AdminOrderController::class,'CanceldOrders']);
    Route::get('admin/hold-orders',[AdminOrderController::class,'HoldOrders']);

    Route::post('admin/order/update/{id}',[AdminOrderController::class,'updateStatus']);
    Route::get('admin/order/view/{id}',[AdminOrderController::class,'orderView']);

    //bank
    Route::get('admin/bank/all',[BankController::class,'index']);
    Route::post('admin/bank/store',[BankController::class,'store']);
    Route::delete('admin/bank/delete/{id}',[BankController::class,'destroy']);

    //all payment request
    Route::get('admin/deposit-history/{status?}',[PaymentHistoryController::class,'history']);
    Route::post('admin/deposit-history/{id}',[PaymentHistoryController::class,'statusUpdate']);

    //all withdraw request
    Route::get('admin/all-withdraw/{status?}',[AdminWithdrawController::class,'index']);
    Route::post('admin/withdraw-paid/{id}',[AdminWithdrawController::class,'paid']);

    //dashboard data
    Route::get('dashboard-datas',[DashboardController::class,'index']);
});


//affiliator

Route::middleware(['auth:sanctum','isAPIaffiliator','userOnline'])->group(function () {

    Route::get('affiliator/profile', [AffiliateController::class, 'AffiliatorProfile']);
    Route::post('affiliator/update/profile', [AffiliateController::class, 'AffiliatorUpdateProfile']);

    Route::get('affiliator/products', [AffiliateController::class, 'AffiliatorProducts']);
    Route::get('single/product/{id}', [AffiliateController::class, 'AffiliatorProductSingle']);
    Route::post('request/product/{id?}', [AffiliateController::class, 'AffiliatorProductRequest']);

    Route::get('affiliator/request/pending/product', [AffiliateController::class, 'AffiliatorProductPendingProduct']);

    Route::get('affiliator/request/active/product', [AffiliateController::class, 'AffiliatorProductActiveProduct']);

    Route::get('affiliator/request/reject/product', [AffiliateController::class, 'AffiliatorProductRejct']);
    Route::get('affiliator/cat/{id}', [AffiliateController::class, 'affiliatorCart']);

    Route::get('single/page/{id}', [AffiliateController::class, 'AffiliatorProductSinglePage']);
    Route::post('add-to-cart', [CartController::class, 'addtocart']);
    Route::get('cart', [CartController::class, 'viewcart']);
    Route::put('cart-updatequantity/{cart_id}/{scope}', [CartController::class, 'updatequantity']);
    Route::delete('delete-cartitem/{cart_id}', [CartController::class, 'deleteCartitem']);
    Route::post('place-order', [CheckoutController::class, 'placeorder']);

    Route::post('order-create',[OrderController::class,'store']);

    Route::get('pending-balance',[BalanceController::class,'PendingBalance']);
    Route::get('active-balance',[BalanceController::class,'ActiveBalance']);

    Route::get('affiliator/all-orders',[OrderController::class,'AllOrders']);
    Route::get('affiliator/pending-orders',[OrderController::class,'pendingOrders']);
    Route::get('affiliator/progress-orders',[OrderController::class,'ProgressOrders']);
    Route::get('affiliator/delivered-orders',[OrderController::class,'DeliveredOrders']);
    Route::get('affiliator/cancel-orders',[OrderController::class,'CanceldOrders']);
    Route::get('affiliator/hold-orders',[OrderController::class,'HoldOrders']);

    Route::get('affiliator/order/view/{id}',[OrderController::class,'orderView']);


    //pending balance
    Route::get('affiliator/balance/history/{status?}',[PendingBalanceController::class,'balance']);

    //bank show
    Route::get('affiliator/banks',[AffiliateBankController::class,'index']);


    Route::post('affiliator/withdraw-post',[WithdrawController::class,'withdraw']);
    Route::get('affiliator/all-withdraw/{status?}',[WithdrawController::class,'index']);

    // Route::get('affiliator/pending-amount',[WithdrawController::class,''])

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('test',function(Request $request){
  $data =   $request->all();

  return collect($data)->where(
    'size','!=',null,
  )->where(
    'color','!=',null
  )->pluck('id','qty');
});

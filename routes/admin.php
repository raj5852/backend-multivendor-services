<?php

use App\Http\Controllers\API\Admin\BankController;
use App\Http\Controllers\API\Admin\CouponController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Admin\PaymentHistoryController;
use App\Http\Controllers\API\Admin\WithdrawController as AdminWithdrawController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdminController;
use Illuminate\Support\Facades\Route;





//admin route
Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function () {

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
    Route::get('admin/order-vs-revenue',[DashboardController::class,'orderVsRevenue']);
    Route::get('admin/recent-order',[DashboardController::class,'recentOrder']);

    Route::get('admin/category-status',[DashboardController::class,'categoryStatus']);

    Route::resource('coupon',CouponController::class);

});

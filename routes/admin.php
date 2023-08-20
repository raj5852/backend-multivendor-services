<?php

use App\Http\Controllers\API\Admin\BankController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\HomeController;
use App\Http\Controllers\API\Admin\HomeSliderController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Admin\OrganizationController;
use App\Http\Controllers\API\Admin\OrganizationTwoController;
use App\Http\Controllers\API\Admin\PaymentHistoryController;
use App\Http\Controllers\Api\Admin\ProductStatusController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\API\Admin\WithdrawController as AdminWithdrawController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Models\OrganizationTwo;
// use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

//admin route
Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function () {

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });
    Route::get('/profile', [ProfileController::class, 'AdminProfile']);
    Route::post('/update/profile', [ProfileController::class, 'AdminUpdateProfile']);

    Route::get('/request/product/pending', [ProductStatusController::class, 'AdminRequestPending']);
    Route::get('/request/product/active', [ProductStatusController::class, 'AdminRequestActive']);
    Route::get('/request/product/all', [ProductStatusController::class, 'AdminRequestAll']);

    Route::get('/request/product/rejected', [ProductStatusController::class, 'RequestRejected']);
    // Route::get('admin/request/product/view/{id}',[AdminController::class,'RequestView']);
    Route::post('/request/product-update/{id}', [ProductStatusController::class, 'RequestUpdate']);


    Route::get('/request/product/view/{id}', [ProductStatusController::class, 'AdminRequestView']);

    Route::get('/request/balances', [ProductStatusController::class, 'AdminRequestBalances']);
    Route::get('/request/balance/active', [ProductStatusController::class, 'AdminRequestBalanceActive']);

    Route::get('product-approval/{id}', [ProductController::class, 'approval']);
    Route::get('product-reject/{id}', [ProductController::class, 'reject']);
    Route::get('all/product-accepted/{id}', [ProductController::class, 'Accepted']);



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

    Route::get('/all-orders',[AdminOrderController::class,'allOrders']);
    Route::get('/pending-orders',[AdminOrderController::class,'pendingOrders']);
    Route::get('/progress-orders',[AdminOrderController::class,'ProgressOrders']);
    Route::get('/delivered-orders',[AdminOrderController::class,'DeliveredOrders']);
    Route::get('/cancel-orders',[AdminOrderController::class,'CanceldOrders']);
    Route::get('/hold-orders',[AdminOrderController::class,'HoldOrders']);

    Route::post('/order/update/{id}',[AdminOrderController::class,'updateStatus']);
    Route::get('/order/view/{id}',[AdminOrderController::class,'orderView']);

    //bank
    Route::get('/bank/all',[BankController::class,'index']);
    Route::post('/bank/store',[BankController::class,'store']);
    Route::delete('/bank/delete/{id}',[BankController::class,'destroy']);

    //all payment request
    Route::get('/deposit-history/{status?}',[PaymentHistoryController::class,'history']);
    Route::post('/deposit-history/{id}',[PaymentHistoryController::class,'statusUpdate']);

    //all withdraw request
    Route::get('/all-withdraw/{status?}',[AdminWithdrawController::class,'index']);
    Route::post('/withdraw-paid/{id}',[AdminWithdrawController::class,'paid']);

    //dashboard data
    Route::get('dashboard-datas',[DashboardController::class,'index']);
    Route::get('/order-vs-revenue',[DashboardController::class,'orderVsRevenue']);
    Route::get('/recent-order',[DashboardController::class,'recentOrder']);

    Route::get('/category-status',[DashboardController::class,'categoryStatus']);


    // Home Page
    Route::get('/home', [HomeSliderController::class, 'index']);
    Route::post('/store-slider', [HomeSliderController::class, 'storeSlider']);
    Route::get('/edit-slider/{id}', [HomeSliderController::class, 'editSlider']);
    Route::post('/update-slider/{id}', [HomeSliderController::class, 'updateSlider']);
    Route::get('/delete-slider/{id}', [HomeSliderController::class, 'deleteSlider']);

    // our Organization
    Route::get('/organization', [OrganizationController::class, 'index']);
    Route::post('/store-organization', [OrganizationController::class, 'storeOrganization']);
    Route::get('/edit-organization/{id}', [OrganizationController::class, 'editOrganization']);
    Route::post('/update-organization/{id}', [OrganizationController::class, 'updateOrganization']);
    Route::get('/delete-organization/{id}', [OrganizationController::class, 'deleteOrganization']);
    // Organization Two

    Route::get('/organizationTwo', [OrganizationTwoController::class, 'index']);
    Route::post('/store-organizationTwo', [OrganizationTwoController::class, 'storeOrganizationTwo']);
    Route::get('/edit-organizationTwo/{id}', [OrganizationTwoController::class, 'editOrganizationTwo']);
    Route::post('/update-organizationTwo/{id}', [OrganizationTwoController::class, 'updateOrganizationTwo']);
    Route::get('/delete-organizationTwo/{id}', [OrganizationTwoController::class, 'deleteOrganizationTwo']);

});


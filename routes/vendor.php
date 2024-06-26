<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SizeController;
use App\Http\Controllers\API\ColorController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\Vendor\AdvancePaymentController;
use App\Http\Controllers\API\Vendor\VendorController;
use App\Http\Controllers\API\Vendor\ProfileController;
use App\Http\Controllers\API\Vendor\ProductManageController;
use App\Http\Controllers\API\Vendor\ProductStatusController;
use App\Http\Controllers\API\Vendor\PaymentRequestController;
use App\Http\Controllers\API\Vendor\RequestProductController;
use App\Http\Controllers\API\Vendor\ServiceCategoryController;
use App\Http\Controllers\API\Vendor\ServiceSubCategoryController;
use App\Http\Controllers\API\Vendor\BankController as VendorBankController;
use App\Http\Controllers\API\Vendor\BrandController as VendorBrandController;

use App\Http\Controllers\API\Vendor\OrderController as VendorOrderController;

use App\Http\Controllers\API\Vendor\DashboardController as VendorDashboardController;


// vendor
Route::middleware(['auth:sanctum','isAPIVendor',])->group(function () {

    Route::get('/checkingAuthenticatedVendor', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });

    Route::get('vendor/profile', [ProfileController::class, 'VendorProfile']);
    Route::post('vendor/update/profile', [ProfileController::class, 'VendorUpdateProfile']);

    //vendor product
    Route::get('vendor/product/{status?}', [ProductManageController::class, 'VendorProduct']);
    Route::get('vendor-product-count/{status?}', [ProductManageController::class, 'VendorProductCount']);
    Route::post('vendor-store-product', [ProductManageController::class, 'VendorProductStore']);
    Route::get('vendor-edit-product/{id}', [ProductManageController::class, 'VendorProductEdit']);
    Route::get('vendor-edit-product-count', [ProductManageController::class, 'vendorProductEditCount']);
    Route::post('vendor-update-product/{id}', [ProductManageController::class, 'VendotUpdateProduct']);
    Route::delete('vendor-delete-product/{id}', [ProductManageController::class, 'VendorDelete']);
    Route::delete('vendor-delete-image/{id}', [ProductManageController::class, 'VendorDeleteImage']);


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

    Route::get('vendor/balabrandnce/request', [ProductStatusController::class, 'VendorBalanceRequest']);
    Route::post('vendor/request/sent', [ProductStatusController::class, 'VendorRequestSent']);

    Route::get('vendor-product-approval/{id}', [ProductStatusController::class, 'approval']);
    Route::get('vendor-product-reject/{id}', [ProductStatusController::class, 'reject']);
    Route::get('vendor-all/product-accepted/{id}', [ProductStatusController::class, 'Accepted']);
    Route::get('vendor-product-status-count', [ProductStatusController::class, 'statusCount']);


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
    Route::get('affiliator/request-count-{status?}', [RequestProductController::class, 'affiliateRequestCount']);
    Route::get('affiliator/request/product/pending', [RequestProductController::class, 'RequestPending']);
    Route::get('affiliator/request/product/active', [RequestProductController::class, 'RequestActive']);
    Route::get('affiliator/request/product/all', [RequestProductController::class, 'RequestAll']);
    Route::get('affiliator/request/product/rejected', [RequestProductController::class, 'RequestRejected']);
    Route::get('affiliator/request/product/view/{id}',[RequestProductController ::class,'RequestView']);
    Route::post('affiliator/product-update/{id}', [RequestProductController::class, 'RequestUpdate']);
    Route::get('affiliator/membership-expire-product', [RequestProductController::class, 'membershipexpireactiveproduct']);
    Route::get('affiliator/membership-expire-product-count', [RequestProductController::class, 'membershipexpireactiveproductCount']);

    //
    //afi orders api
    Route::get('vendor/all-orders',[VendorOrderController::class,'AllOrders']);
    Route::get('vendor/pending-orders',[VendorOrderController::class,'pendingOrders']);
    Route::get('vendor/progress-orders',[VendorOrderController::class,'ProgressOrders']);
    Route::get('vendor/received-orders',[VendorOrderController::class,'receivedOrders']);
    Route::get('vendor/delivered-orders',[VendorOrderController::class,'DeliveredOrders']);
    Route::get('vendor/cancel-orders',[VendorOrderController::class,'CanceldOrders']);
    Route::get('vendor/hold-orders',[VendorOrderController::class,'HoldOrders']);
    Route::get('vendor/order-count',[VendorOrderController::class,'orderCount']);

    Route::post('vendor/order/update/{id}',[VendorOrderController::class,'productorderstatus']);
    Route::get('vendor/order/view/{id}',[VendorOrderController::class,'orderView']);

    //bank show
    Route::get('vendor/banks',[VendorBankController::class,'index']);

    //vendor payment request
    Route::post('vendor/payment/submit',[PaymentRequestController::class,'store']);
    Route::get('vendor/payment/history/{status?}',[PaymentRequestController::class,'history']);

    Route::get('vendor/dashboard-datas',[VendorDashboardController ::class,'index']);
    Route::get('vendor/order-vs-revenue',[VendorDashboardController::class,'orderVsRevenue']);

    // top 10 item
    Route::get('vendor/top-ten-items',[VendorDashboardController::class,'topten']);


    Route::prefix('vendor')->group(function(){

        Route::get('product-edit-requests',[ProductManageController::class,'vendorproducteditrequest']);
        Route::get('product-edit-requests-count',[ProductManageController::class,'vendorproducteditrequestCount']);
        Route::get('product-edit-requests/{id}',[ProductManageController::class,'vendorproducteditrequestview']);
        Route::get('transition-history-advance-payment',[AdvancePaymentController::class,'store']);
    });




});

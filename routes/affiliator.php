<?php
use App\Http\Controllers\API\Affiliate\BalanceController;
use App\Http\Controllers\API\Affiliate\BankController as AffiliateBankController;
use App\Http\Controllers\API\Affiliate\CartController;
use App\Http\Controllers\API\Affiliate\CheckoutController;
use App\Http\Controllers\API\Affiliate\DashboardController as AffiliateDashboardController;
use App\Http\Controllers\API\Affiliate\OrderController;
use App\Http\Controllers\API\Affiliate\PendingBalanceController;
use App\Http\Controllers\API\Affiliate\ProductStatusController;
use App\Http\Controllers\API\Affiliate\ProfileController;
use App\Http\Controllers\API\Affiliate\SingleProductController;
use App\Http\Controllers\API\Affiliate\WithdrawController;
use Illuminate\Support\Facades\Route;




//affiliator

Route::middleware(['auth:sanctum','isAPIaffiliator','userOnline'])->group(function () {

    Route::get('affiliator/profile', [ProfileController::class, 'AffiliatorProfile']);
    Route::post('affiliator/update/profile', [ProfileController::class, 'AffiliatorUpdateProfile']);

    Route::get('affiliator/products', [ProductStatusController::class, 'AffiliatorProducts']);
    Route::get('single/product/{id}', [SingleProductController::class, 'AffiliatorProductSingle']);
    Route::get('single/active/product/{id}', [SingleProductController::class, 'AffiliatoractiveProduct']);

    Route::post('request/product/{id?}', [ProductStatusController::class, 'AffiliatorProductRequest']);

    Route::get('affiliator/request/pending/product', [ProductStatusController::class, 'AffiliatorProductPendingProduct']);

    Route::get('affiliator/request/active/product', [ProductStatusController::class, 'AffiliatorProductActiveProduct']);
    Route::get('affiliator/vendor-expire-products', [ProductStatusController::class, 'vendorexpireproducts']);

    Route::get('affiliator/request/reject/product', [ProductStatusController::class, 'AffiliatorProductRejct']);
    Route::get('affiliator/cat/{id}', [CartController::class, 'affiliatorCart']);

    Route::get('single/page/{id}', [SingleProductController::class, 'AffiliatorProductSinglePage']);
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

    Route::get('affiliator/dashboard-datas',[AffiliateDashboardController::class,'index']);
    Route::get('affiliator/order-vs-comission',[AffiliateDashboardController::class,'orderVsRevenue']);

});

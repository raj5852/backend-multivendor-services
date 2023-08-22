<?php

use App\Http\Controllers\API\Admin\BankController;
use App\Http\Controllers\API\Admin\CompanionController;
use App\Http\Controllers\API\Admin\ContactController;
use App\Http\Controllers\API\Admin\CouponController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\FaqController;
use App\Http\Controllers\API\Admin\FooterMediaController;
use App\Http\Controllers\API\Admin\HomeBannerController;
use App\Http\Controllers\API\Admin\MembersController;
use App\Http\Controllers\API\Admin\MissionController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Admin\OrganizationController;
use App\Http\Controllers\API\Admin\OrganizationTwoController;
use App\Http\Controllers\API\Admin\OurServiceController;
use App\Http\Controllers\API\Admin\PartnerController;
use App\Http\Controllers\API\Admin\PaymentHistoryController;
use App\Http\Controllers\Api\Admin\ProductStatusController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\API\Admin\SettingsController;
use App\Http\Controllers\API\Admin\SupportBoxCategoryController;
use App\Http\Controllers\API\Admin\SupportBoxController;
use App\Http\Controllers\API\Admin\SupportProblemTopicController;
use App\Http\Controllers\API\Admin\TestimonialController;
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

//admin route
Route::prefix('admin')->middleware(['auth:sanctum', 'isAPIAdmin'])->group(function () {

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
    Route::get('/home-banner', [HomeBannerController::class, 'index']);
    Route::post('/home-banner-store', [HomeBannerController::class, 'storeHomeBanner']);
    Route::get('/home-banner-edit/{id}', [HomeBannerController::class, 'editHomeBanner']);
    Route::post('/home-banner-update/{id}', [HomeBannerController::class, 'updateHomeBanner']);
    Route::get('/home-banner-delete/{id}', [HomeBannerController::class, 'deleteHomeBanner']);

    // our Organization
    Route::get('/organization', [OrganizationController::class, 'index']);
    Route::post('/organization-store', [OrganizationController::class, 'storeOrganization']);
    Route::get('/organization-show/{id}', [OrganizationController::class, 'showOrganization']);
    Route::get('/organization-edit/{id}', [OrganizationController::class, 'editOrganization']);
    Route::post('/organization-update/{id}', [OrganizationController::class, 'updateOrganization']);
    Route::get('/organization-delete/{id}', [OrganizationController::class, 'deleteOrganization']);

    // Organization Two
    Route::get('/organizationTwo', [OrganizationTwoController::class, 'index']);
    Route::post('/organizationTwo-store', [OrganizationTwoController::class, 'storeOrganizationTwo']);
    Route::get('/organizationTwo-show/{id}', [OrganizationTwoController::class, 'showOrganizationTwo']);
    Route::get('/organizationTwo-edit/{id}', [OrganizationTwoController::class, 'editOrganizationTwo']);
    Route::post('/organizationTwo-update/{id}', [OrganizationTwoController::class, 'updateOrganizationTwo']);
    Route::get('/organizationTwo-delete/{id}', [OrganizationTwoController::class, 'deleteOrganizationTwo']);

    // Our Services
    Route::get('/our-services', [OurServiceController::class, 'index']);
    Route::post('/our-services-store', [OurServiceController::class, 'storeOurService']);
    Route::get('/our-services-show/{id}', [OurServiceController::class, 'showOurService']);
    Route::get('/our-services-edit/{id}', [OurServiceController::class, 'editOurService']);
    Route::post('/our-services-update/{id}', [OurServiceController::class, 'updateOurService']);
    Route::get('/our-services-delete/{id}', [OurServiceController::class, 'deleteOurService']);

    // Our Partner
    Route::get('/our-partner', [PartnerController::class, 'index']);
    Route::post('/our-partner-store', [PartnerController::class, 'storeOurPartner']);
    Route::get('/our-partner-show/{id}', [PartnerController::class, 'showOurPartner']);
    Route::get('/our-partner-edit/{id}', [PartnerController::class, 'editOurPartner']);
    Route::post('/our-partner-update/{id}', [PartnerController::class, 'updateOurPartner']);
    Route::get('/our-partner-delete/{id}', [PartnerController::class, 'deleteOurPartner']);

    // Companion section
    Route::get('/companion', [CompanionController::class, 'index']);
    Route::post('/companion-store', [CompanionController::class, 'storeCompanion']);
    Route::get('/companion-show/{id}', [CompanionController::class, 'showCompanion']);
    Route::get('/companion-edit/{id}', [CompanionController::class, 'editCompanion']);
    Route::post('/companion-update/{id}', [CompanionController::class, 'updateCompanion']);
    Route::get('/companion-delete/{id}', [CompanionController::class, 'deleteCompanion']);

    // Member section
    Route::get('/member', [MembersController::class, 'index']);
    Route::post('/member-store', [MembersController::class, 'storeMember']);
    Route::get('/member-show/{id}', [MembersController::class, 'showMember']);
    Route::get('/member-edit/{id}', [MembersController::class, 'editMember']);
    Route::post('/member-update/{id}', [MembersController::class, 'updateMember']);
    Route::get('/member-delete/{id}', [MembersController::class, 'deleteMember']);

    // footer Social Icon / footer-media section
    Route::get('/footer-media', [FooterMediaController::class, 'index']);
    Route::post('/footer-media-store', [FooterMediaController::class, 'storeFooterMedia']);
    Route::get('/footer-media-edit/{id}', [FooterMediaController::class, 'editFooterMedia']);
    Route::post('/footer-media-update/{id}', [FooterMediaController::class, 'updateFooterMedia']);
    Route::get('/footer-media-delete/{id}', [FooterMediaController::class, 'deleteFooterMedia']);

    // front end settings update
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings-update/{id}', [SettingsController::class, 'update']);


    // User Contact Submitted Infos
    Route::get('/contact-messages', [ContactController::class, 'index']);

    Route::get('category-status',[DashboardController::class,'categoryStatus']);

    Route::resource('coupon',CouponController::class);

    Route::resource('faq', FaqController::class);

    Route::resource('mission', MissionController::class);

    Route::resource('testimonial', TestimonialController::class);

    Route::resource('supportboxcategory',SupportBoxCategoryController::class);
    Route::resource('supportproblem-topic',SupportProblemTopicController::class);
    Route::resource('supportbox',SupportBoxController::class);

});


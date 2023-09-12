<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use App\Models\UserSubscription;

/**
 * Class SubscriptionService.
 */
class SubscriptionService
{
    static function store($subscription,$user,$totalamount,$coupon,$paymentmethod)
    {
        $trxid = uniqid();

        $userSubscription = new UserSubscription();
        $userSubscription->user_id = $user->id;
        $userSubscription->trxid = $trxid;
        $userSubscription->subscription_id = $subscription->id;
        $userSubscription->expire_date = membershipexpiredate($subscription->subscription_package_type);

        if (userrole($user->role_as) == 'vendor') {
            $userSubscription->service_qty = $subscription->service_qty;
            $userSubscription->product_qty = $subscription->product_qty;
            $userSubscription->affiliate_request = $subscription->affiliate_request;
        }

        if (userrole($user->role_as) == 'affiliate') {
            $userSubscription->product_request = $subscription->product_request;
            $userSubscription->product_approve = $subscription->product_approve;
            $userSubscription->service_create = $subscription->service_create;
        }

        $userSubscription->save();
        PaymentHistoryService::store($trxid,$totalamount,$paymentmethod,'Subscription','-',$coupon,$user->id);
        if($coupon != ''){
            $getcoupon = Coupon::find($coupon);

            $couponUser = User::find($getcoupon->user_id);
            $couponUser->increment('balance',$getcoupon->commission);


            PaymentHistoryService::store($trxid,$getcoupon->commission,'My wallet','Referral bonus','+',$coupon,$couponUser->id);

        }
    }
}

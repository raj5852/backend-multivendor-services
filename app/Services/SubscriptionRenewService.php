<?php

namespace App\Services;

use App\Models\PaymentStore;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;

/**
 * Class SubscriptionRenewService.
 */
class SubscriptionRenewService
{
    static  function renew($validatedData)
    {
        $user = User::find(userid());
        $subscriptionid = $validatedData['package_id'];
        $trxid = uniqid();
        $getsubscription = Subscription::find($subscriptionid);

        if ($validatedData['payment_method'] == 'my-wallet') {
            return  self::subscriptionadd($user, $subscriptionid, $trxid, 'My wallet', 'Renew');
        }

        if ($validatedData['payment_method'] == 'aamarpay') {
            $successurl = url('api/aaparpay/renew-success');

            $validatedData['user_id'] = auth()->id();

            PaymentStore::create([
                'payment_gateway'=>'aamarpay',
                'trxid'=>$trxid,
                'status'=>'pending',
                'payment_type'=>'renew',
                'info'=>$validatedData,
            ]);


            return AamarPayService::gateway($getsubscription->subscription_amount, $trxid, 'renew', $successurl);
        }
    }



    // $trxid, $amount, $payment_method, $transition_type, $balance_type, $coupon, $userid
    static function subscriptionadd($user, $subscriptionid, $trxid, $payment_method, $transition_type)
    {
        $userCurrentSubscription = $user->usersubscription;
        $getsubscription = Subscription::find($subscriptionid);
        $usersubscriptionPlan = Subscription::find($userCurrentSubscription->subscription_id);
        $addMonth =  getmonth($getsubscription->subscription_package_type);

        PaymentHistoryService::store($trxid, $getsubscription->subscription_amount, $payment_method, $transition_type, '-', null, $user->id);


        if ($getsubscription->plan_type == $usersubscriptionPlan->plan_type) {

            if ($userCurrentSubscription->expire_date > now()) {
                $expiretime = Carbon::parse($userCurrentSubscription->expire_date)->addMonth($addMonth);
            } else {
                $expiretime = now()->addMonth($addMonth);
            }
            $userCurrentSubscription->expire_date = $expiretime;
            $userCurrentSubscription->save();

            return responsejson('Renew successfully');
        } else {
            $expiredate = now()->addMonth($addMonth);

            $userCurrentSubscription->expire_date =  $expiredate;
            $userCurrentSubscription->subscription_id =  $getsubscription->id;

            if (userrole($user->role_as) == 'vendor') {
                $userCurrentSubscription->service_qty =  $getsubscription->service_qty;
                $userCurrentSubscription->product_qty =  $getsubscription->product_qty;
                $userCurrentSubscription->affiliate_request =  $getsubscription->affiliate_request;
            }

            if (userrole($user->role_as) == 'affiliate') {
                $userCurrentSubscription->product_request =  $getsubscription->product_request;
                $userCurrentSubscription->product_approve =  $getsubscription->product_approve;
                $userCurrentSubscription->service_create =  $getsubscription->service_create;
            }
            $userCurrentSubscription->save();

            return  responsejson("Subscription upgrade successfully!");
        }
    }
}

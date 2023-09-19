<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;

/**
 * Class SubscriptionRenewService.
 */
class SubscriptionRenewService
{
    static  function renew($validatedData)
    {
        $user = User::find(userid());
        $subscriptionid = $validatedData['package_id'];

        if ($validatedData['payment_method'] == 'my-wallet') {

            return  self::subscriptionadd($user, $subscriptionid);
        }
    }

    static function subscriptionadd($user, $subscriptionid)
    {
        $userCurrentSubscription = $user->usersubscription;
        $getsubscription = Subscription::find($subscriptionid);
        $usersubscriptionPlan = Subscription::find($userCurrentSubscription->subscription_id);
        $now = now();

        if ($getsubscription->plan_type == $usersubscriptionPlan->plan_type) {
            if($userCurrentSubscription->exp)


            $usersubscriptionPlan->update([]);
        }
    }
}

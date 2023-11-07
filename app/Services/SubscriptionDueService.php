<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

/**
 * Class SubscriptionDueService.
 */
class SubscriptionDueService
{
    static function subscriptiondue(int $userid)
    {
        $user = User::find($userid)?->usersubscription;

        if(!$user){
            return 0;
        }

        $userdate = Carbon::parse($user?->expire_date);
        $currentdate = now();

        if ($userdate < now()) {
            $totaldueday =  $userdate->diffInDays($currentdate);
            $usersubscription =  $user->subscription;
            $userpackagetype =  $usersubscription?->subscription_package_type;

            if ($totaldueday >= 30) {
                $totaldueday = 30;
            }

            if ($userpackagetype == 'monthly') {
                $amount = ($usersubscription->subscription_amount / 30);
            } elseif ($userpackagetype == 'half_yearly') {
                $amount = ($usersubscription->subscription_amount / 180);
            } elseif ($userpackagetype == 'yearly') {
                $amount = ($usersubscription->subscription_amount / 360);
            }

            $totaldueable = ($totaldueday * $amount);
        } else {
            $totaldueable = 0;
        }
        return $totaldueable;
    }
}

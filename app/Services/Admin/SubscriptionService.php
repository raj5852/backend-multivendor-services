<?php

namespace App\Services\Admin;

use App\Models\Subscription;

/**
 * Class SubscriptionService.
 */
class SubscriptionService
{
    public static function create($validateData)
    {
       $subscription =new  Subscription();
       $subscription->card_symbol_icon          = $validateData['card_symbol_icon'];
       $subscription->card_time                 = $validateData['card_time'];
       $subscription->card_heading              = $validateData['card_heading'];
       $subscription->card_facilities_title     = $validateData['card_facilities_title'];
       $subscription->save();
       return true;
    }



    public static function update($validateData, $id)
    {
       $subscription = Subscription::find($id);
       $subscription->card_symbol_icon          = $validateData['card_symbol_icon'];
       $subscription->card_time                 = $validateData['card_time'];
       $subscription->card_heading              = $validateData['card_heading'];
       $subscription->card_facilities_title     = $validateData['card_facilities_title'];
       $subscription->save();
       return true;
    }
}

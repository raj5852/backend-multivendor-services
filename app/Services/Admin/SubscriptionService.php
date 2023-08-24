<?php

namespace App\Services\Admin;

use App\Models\Subscription;

/**
 * Class SubscriptionService.
 */
class SubscriptionService
{

    public static function update($validateData, $id)
    {
        $values = [
            $value1 = request('card_facilities_icon'),
            $value2 = request('card_facilities_title'),
        ];


       $subscription = Subscription::find($id);
       $subscription->card_symbol_icon          = $validateData['card_symbol_icon'];
       $subscription->card_time                 = $validateData['card_time'];
       $subscription->card_heading              = $validateData['card_heading'];
       $subscription->card_facilities_title     = json_encode($values);
       $subscription->save();
       return true;
    }
}

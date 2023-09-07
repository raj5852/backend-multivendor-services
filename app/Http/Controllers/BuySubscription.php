<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuysubscriptionRequest;
use App\Http\Requests\CouponApplyRequest;
use App\Models\Coupon as ModelsCoupon;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SosService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class BuySubscription extends Controller
{
    function buy(int $id)
    {
        $subscription =  Subscription::findOr($id, function () {
            return responsejson('Not found', 404);
        });
        $user = User::find(userid());

        if ($subscription->subscription_user_type !=  userrole($user->role_as)) {
            return responsejson('This subscription is not for you');
        }

        return $this->response($subscription);
    }

    function coupon(CouponApplyRequest $request)
    {
        $validateData = $request->validated();
        $coupon = ModelsCoupon::where('name', $validateData['name'])->first();
        return $this->response($coupon);
    }

    function buysubscription(BuysubscriptionRequest $request)
    {
        $validateData = $request->validated();
        $subscription = Subscription::find($validateData['subscription_id']);
        $amount = $subscription->subscription_amount;



        if (request()->has('coupon_name')) {
            $coupon = ModelsCoupon::query()
                ->where('name', request('coupon_name'))
                ->where('status', 'active')
                ->whereDate('expire_date', '>', now())
                ->first();

            if (!$coupon) {
                return responsejson('Something is wrong', 'fail');
            }

            if ($coupon->type == 'flat') {
                $amount = ($amount - $coupon->amount);
            } else {
                $amount = ($amount / 100) * $coupon->amount;
            }
        }



        if ($validateData['payment_type'] == 'aamarpay') {
            return  SosService::aamarpay($amount, $validateData);
        } else {
            $user = User::find(userid());
            $balance = $user->balance;

            if (convertfloat($balance) > $amount) {

                SubscriptionService::store($subscription,$user);

                $user->balance = (convertfloat($user->balance) - $amount);
                $user->save();

            } else {
                return responsejson('Not enough balance', 'fail');
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuysubscriptionRequest;
use App\Http\Requests\CouponApplyRequest;
use App\Models\Coupon as ModelsCoupon;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\PaymentHistoryService;
use App\Services\SosService;
use App\Services\SubscriptionDueService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class BuySubscription extends Controller
{
    function buy(int $id)
    {
        $subscription =  Subscription::findOr($id, function () {
            return responsejson('Not found', 404);
        });

        $user = User::find(auth()->id())->usersubscription;
        $proviousdue = SubscriptionDueService::subscriptiondue(auth()->id());

        return response()->json(
            [
                'data' => 'success',
                'message' => $subscription,
                'previous_due' => $proviousdue
            ]
        );
    }

    function coupon(CouponApplyRequest $request)
    {
        $validateData = $request->validated();
        $coupon = ModelsCoupon::where('name', $validateData['name'])->select('id', 'amount', 'type')->first();
        return $this->response($coupon);
    }

    function buysubscription(BuysubscriptionRequest $request)
    {
        $validateData = $request->validated();

        $user = User::find(userid());

        if ($user->usersubscription) {
            return responsejson('You have a subscription. You can not buy again.', 'fail');
        }

        $subscription = Subscription::find($validateData['subscription_id']);
        $amount = $subscription->subscription_amount;


        $coupon = null;
        if (request('coupon_id') != '') {
            $couponUsed = ModelsCoupon::withCount('couponused')->find(request('coupon_id'));

            $coupon = ModelsCoupon::query()
                ->where('id', request('coupon_id'))
                ->where('status', 'active')
                ->where('limitation', '>', $couponUsed->couponused_count)
                ->whereDate('expire_date', '>', now())
                ->first();

            if (!$coupon) {
                return responsejson('Coupon not available', 'fail');
            }

            if ($coupon->type == 'flat') {
                $amount = ($amount - $coupon->amount);
            } else {
                $amount = ($amount - (($amount / 100) * $coupon->amount));
            }
        }



        if ($validateData['payment_type'] == 'aamarpay') {
            return  SosService::aamarpaysubscription($amount, $validateData, $coupon?->id);
        } else {
            $balance = $user->balance;

            if (convertfloat($balance) >= $amount) {

                if (request('payment_type') == 'free') {
                    $paymentmethod = "free";
                } elseif (request('payment_type') == 'my-wallet') {
                    $paymentmethod = "My wallet";
                }
                $data =  SubscriptionService::store($subscription, $user, $amount, $coupon?->id, $paymentmethod);

                $user->balance = (convertfloat($user->balance) - $amount);
                $user->save();

                if ($data == '2' || $data == '3') {
                    $path = paymentredirect($data);
                    return config('app.redirecturl') . $path . '?message=successful';
                }

                return $data;
            } else {
                return responsejson('Not enough balance', 'fail');
            }
        }
    }
}

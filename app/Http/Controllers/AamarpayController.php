<?php

namespace App\Http\Controllers;

use App\Models\AdminAdvertise;
use App\Models\PaymentStore;
use App\Models\ServiceOrder;
use App\Models\VendorService;
use App\Models\CustomerRequiremnt;
use App\Models\DollerRate;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PaymentHistoryService;
use App\Services\SubscriptionRenewService;
use App\Services\SubscriptionService;

class AamarpayController extends Controller
{

    function servicesuccess()
    {
        $response = request()->all();
        $vendorservice = ServiceOrder::where('trxid', $response['mer_txnid'])->first();
        $vendorservice->update([
            'is_paid' => 1
        ]);
        PaymentHistoryService::store($vendorservice->trxid, $vendorservice->amount, 'Ammarpay', 'Service', '-', '', $vendorservice->user_id);
    }

    function renewsuccess(){
        $response = request()->all();
        $data = PaymentStore::where(['trxid' => $response['mer_txnid'], 'status' => 'pending'])->first();
        $user =  User::find($data['info']['user_id']);
        $subscriptionid =  $data['info']['package_id'];
        $trxid = $data->trxid;
        $payment_method = 'Aamarpay';
        $transition_type = '-';
        SubscriptionRenewService::subscriptionadd($user,$subscriptionid,$trxid,$payment_method,$transition_type);
        return "success then redirect";
    }
    function advertisesuccess()
    {
        $response = request()->all();
        $adminAdvertise = AdminAdvertise::where('trxid', $response['mer_txnid'])->first();

        $adminAdvertise->update([
            'is_paid' => 1
        ]);
        $dollerRate  =  DollerRate::first()?->amount;

        PaymentHistoryService::store($adminAdvertise->trxid, ($adminAdvertise->budget_amount * $dollerRate), 'Ammarpay', 'Advertise', '-', '', $adminAdvertise->user_id);


        return 'redirect';
    }

    function subscriptionsuccess()
    {
         $response = request()->all();

        $data = PaymentStore::where(['trxid' => $response['mer_txnid'], 'status' => 'pending'])->first();
        $validatedData  =  $data['info'];
        $subscription = Subscription::find($validatedData['subscription_id']);
        $user = User::find($validatedData['user_id']);
        $couponid = $validatedData['coupon_id'];

        if (!$data) {
            return false;
        }

        if ($response['opt_a'] == 'subscription') {
            $amount = $response['amount_original'];
            SubscriptionService::store($subscription,$user,$amount,$couponid,'Aamarpay');

        }
        "success";
    }



    function fail()
    {
        return response()->json('fai');
    }

    function cancel()
    {
        return response()->json('cancel');
    }
}

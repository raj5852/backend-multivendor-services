<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RechargeRequest;
use App\Models\PaymentStore;
use App\Services\AamarPayService;
use Illuminate\Http\Request;

class RechargeController extends Controller
{
    function recharge(RechargeRequest $request){

        $validateData = $request->validated();
        $validateData['user_id'] = auth()->id();

        $trxid = uniqid();
        $type = "recharge";
        $successurl = url('api/aaparpay/recharge-success');

        PaymentStore::create([
            'payment_gateway' => 'aamarpay',
            'trxid' => $trxid,
            'payment_type' => 'recharge',
            'info' => $validateData,
            'customer_requirement_id' => 0,
        ]);
        // return 2;

     return   AamarPayService::gateway($validateData['amount'],$trxid,$type,$successurl);
    }
}

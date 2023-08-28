<?php

namespace App\Http\Controllers;

use App\Models\PaymentStore;
use App\Models\ServiceOrder;

class AamarpayController extends Controller
{

    function success()
    {
        $response = request()->all();
        $data = PaymentStore::where(['trxid' => $response['mer_txnid'], 'status' => 'pending'])->first();
        if (!$data) {
            return false;
        }
        ServiceOrder::create([
            'user_id' => $data['info']['user_id'],
            'vendor_service_id' => $data['info']['user_id'],
            'service_package_id' => $data['info']['service_package_id'],
        ]);

        return 'it will redirect';
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

<?php

namespace App\Http\Controllers;

use App\Models\PaymentStore;
use App\Models\ServiceOrder;
use App\Models\VendorService;
use App\Models\CustomerRequiremnt;
use App\Models\ServicePackage;

class AamarpayController extends Controller
{

    function success()
    {
        $response = request()->all();

        $data = PaymentStore::where(['trxid' => $response['mer_txnid'], 'status' => 'pending'])->first();



        if (!$data) {
            return false;
        }
        $vendorService = VendorService::find($data['info']['vendor_service_id']);

        $serviceOrder =  ServiceOrder::create([
            'user_id' => $data['info']['user_id'],
            'vendor_id' => $vendorService->user_id,
            'vendor_service_id' => $data['info']['vendor_service_id'],
            'service_package_id' => $data['info']['service_package_id'],
            'amount'=> $response['amount'],
            'commission_amount'=>  $vendorService->commission,
            'commission_type'=> $vendorService->commission_type
        ]);

        $requirement =  CustomerRequiremnt::where('uniquid', $data['info']['customer_requirement_id'])->exists();

        if ($requirement) {
            CustomerRequiremnt::where('uniquid', $data['info']['customer_requirement_id'])->update([
                'vendor_service_id' => $serviceOrder->id
            ]);
        }

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

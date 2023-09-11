<?php

namespace App\Http\Controllers;

use App\Models\AdminAdvertise;
use App\Models\PaymentStore;
use App\Models\ServiceOrder;
use App\Models\VendorService;
use App\Models\CustomerRequiremnt;
use App\Models\DollerRate;
use App\Models\ServicePackage;
use App\Services\PaymentHistoryService;

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

    function success()
    {
        $response = request()->all();

        return  $data = PaymentStore::where(['trxid' => $response['mer_txnid'], 'status' => 'pending'])->first();

        if (!$data) {
            return false;
        }

        if ($response['opt_a'] == 'service') {
            $this->service($data, $response);
        }

        if ($response['opt_a'] == 'subscription') {
            $this->subscription($data, $response);
        }
    }

    function subscription($data, $response)
    {
    }

    function service($data, $response)
    {

        $vendorService = VendorService::find($data['info']['vendor_service_id']);

        $serviceOrder =  ServiceOrder::create([
            'user_id' => $data['info']['user_id'],
            'vendor_id' => $vendorService->user_id,
            'vendor_service_id' => $data['info']['vendor_service_id'],
            'service_package_id' => $data['info']['service_package_id'],
            'amount' => $response['amount'],
            'commission_amount' =>  $vendorService->commission,
            'commission_type' => $vendorService->commission_type
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

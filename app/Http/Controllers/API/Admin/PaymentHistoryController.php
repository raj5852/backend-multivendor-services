<?php

namespace App\Http\Controllers\API\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentHistoryController extends Controller
{
    //
    function history(){
        $data =  VendorPaymentRequest::latest()
        ->when(request('search'),fn($q, $name)=>$q->where('transition_id','like',"%{$name}%"))

        // ->when(request('status') == 'rejected', function ($q) {
        //     return $q->where('status', 'rejected');
        // })

        // ->when(request('status') == 'rejected', function ($q) {
        //     return $q->where('status', 'rejected');
        // })

        ->with('vendor:id,name')
        ->paginate(10);
        return response()->json([
            'status'=>200,
            'message'=>$data
        ]);

    }

    function statusUpdate(Request $request,$id){




        $data = VendorPaymentRequest::find($id);
        if($data){

            if($request->status == Status::Success->value){

                if($data->status == Status::Pending->value){
                    $data->status = Status::Success->value;

                    //add balance
                    $vendor = User::find($data->vendor_id);
                    $vendor->balance = ($vendor->balance + $data->balance);
                    $vendor->save();
                }

            }

            if($request->status == Status::Cancel->value){
                $data->status = Status::Cancel->value;
            }




            return response()->json([
                'status'=>200,
                'message'=>'Balance added to vendor'
            ]);


        }else{
            return response()->json([
                'status'=>404,
                'message'=>'Payment not found'
            ]);

        }
    }
}

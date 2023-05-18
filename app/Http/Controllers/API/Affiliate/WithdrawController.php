<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    //
    function index(){
        $withdraw = Withdraw::where('affiliator_id',auth()->user()->id)
        ->latest()
        ->when(request('search'),fn($q, $name)=>$q->where('bank_name','like',"%{$name}%"))

        ->when(request('status') == 'success', function ($q) {
            return $q->where('status', 'success');
        })

        ->when(request('status') == 'pending', function ($q) {
            return $q->where('status', 'pending');
        })
        ->paginate(10)
        ->withQueryString();



        return response()->json([
            'status'=>200,
            'message'=>$withdraw
        ]);

    }

    function withdraw(Request $request){
        $validator = Validator::make($request->all(),[
            'amount'=>'required|integer',
            // 'name'=>'required',
            'bank_name'=>'required',
            'ac_or_number'=>'required',
            // 'holder_name'=>'required',
            // 'branch_name'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>401,
                'message'=>$validator->messages()
            ]);
        }
        if(auth()->user()->balance >= $request->amount){

            Withdraw::create([
                'affiliator_id'=>auth()->user()->id,
                'amount'=>$request->amount,
                // 'name'=>$request->name,
                'bank_name'=>$request->bank_name,
                'ac_or_number'=>$request->ac_or_number,
                'holder_name'=>$request->holder_name,
                'branch_name'=>$request->branch_name,
            ]);

            return response()->json([
                'status'=>200,
                'message' => 'Withdraw successfully!'
            ]);


        }else{

            return response()->json([
                'status'=>200,
                'message' => 'Balance Not Available!'
            ]);


        }


    }
}

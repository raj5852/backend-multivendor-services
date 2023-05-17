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
        ->paginate(10)
        ->withQueryString();



        return response()->json([
            'status'=>200,
            'message'=>$withdraw
        ]);

    }

    function withdraw(Request $request){
        $validator = Validator::make($request->all(),[
            'affiliator_id'=>'required|integer',
            'amount'=>'required|integer',
            'name'=>'required',
            'bank_name'=>'required',
            'ac_or_number'=>'required',
            'holder_name'=>'required',
            'branch_name'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>401,
                'message'=>$validator->messages()
            ]);
        }
        Withdraw::create([
            'affiliator_id'=>$request->affiliator_id,
            'amount'=>$request->amount,
            'name'=>$request->name,
            'bank_name'=>$request->bank_name,
            'ac_or_number'=>$request->ac_or_number,
            'holder_name'=>$request->holder_name,
            'branch_name'=>$request->branch_name,
        ]);

        return response()->json([
            'status'=>200,
            'message' => 'Withdraw successfully!'
        ]);

    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductDetails;
use App\Models\User;
use Illuminate\Http\Request;

class ProductStatusController extends Controller
{

    public function AdminRequestPending()
    {

         $product=ProductDetails::with(['vendor','affiliator','product'])
        ->where('status','2')
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }


    public function AdminRequestActive()
    {
         $product=ProductDetails::with(['vendor','affiliator','product'])->where('status','1')
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })

        ->latest()->paginate(10)
        ->withQueryString();

        return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }


    function AdminRequestAll(){
        $product=ProductDetails::with(['vendor','affiliator','product'])
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })

        ->latest()->paginate(10)
        ->withQueryString();

        return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);

        // return ProductDetails::with('product')->get();
    }


    function RequestRejected(){

        $product=ProductDetails::with(['vendor','affiliator','product'])
        ->where('status','3')
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }


    function RequestUpdate(Request $request,$id){

        $data =  ProductDetails::find($id);
        if ($data) {
            $data->status = $request->status;
                $data->reason = $request->reason;
            $data->save();
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Not found',
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'updated successfully',
        ]);
    }


    function AdminRequestView($id){
        $product=ProductDetails::with(['vendor','affiliator','product' => function($query) {
            $query->with('productImage','sizes','colors');
        }])->find($id);


            return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }


    public function AdminRequestBalances()
    {
        $user=User::where('balance_status',0)->get();
        return response()->json($user);
    }


    public function AdminRequestBalanceActive()
    {
        $user=User::where('balance_status',1)->get();
        return response()->json($user);
    }


}

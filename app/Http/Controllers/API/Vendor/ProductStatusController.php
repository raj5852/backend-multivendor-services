<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductDetails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStatusController extends Controller
{
    public function VendorBalanceRequest()
    {
        $vendor = User::find(Auth::user()->id);
        return response()->json([
            'status' => 200,
            'vendor' => $vendor
        ]);
    }

    public function VendorRequestSent(Request $request)
    {

        $data = User::find(Auth::user()->id);
        $data->balance = $request->balance;
        $data->message = $request->message;
        $data->balance_status = 0;
        $data->update();

        return response()->json([
            'status' => 200,
            'message' => 'Vendor Balance Added  Successfully Please Wait',
        ]);
    }

    public function approval($id)
    {
        $product = ProductDetails::find($id);
        if ($product->status == '2') {
            $product->status = '1';
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product is Accepted ',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Product is Request Accepted',
            ]);
        }
    }


    public function reject($id)
    {
        $product = ProductDetails::find($id);
        if ($product->status == '2') {
            $product->status = '3';
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product is Reject ',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Product is Request Accepted',
            ]);
        }
    }


    public function Accepted($id)
    {
        $product = ProductDetails::find($id);
        if ($product->status == '2') {
            $product->status = '1';
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product is active ',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Product is Inactive',
            ]);
        }
    }


}

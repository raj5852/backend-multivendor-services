<?php

namespace App\Http\Controllers\API\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    function allOrders()
    {
        $orders = Order::searchProduct()
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString();



        $orders->map(function ($order) {
            $order->variants = json_decode($order->variants);
            return $order;
        });


        return response()->json([
            'status' => 200,
            'message' => $orders
        ]);
    }

    function pendingOrders()
    {
        $orders = Order::searchProduct()
            ->where('status', Status::Pending->value)
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString();



        $orders->map(function ($order) {
            $order->variants = json_decode($order->variants);
            return $order;
        });

        return response()->json([
            'status' => 200,
            'message' => $orders
        ]);
    }

    function ProgressOrders()
    {
        $orders = Order::searchProduct()
            ->where('status', Status::Progress->value)
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name'])

            ->latest()
            ->paginate(10)
            ->withQueryString();



        $orders->map(function ($order) {
            $order->variants = json_decode($order->variants);
            return $order;
        });


        return response()->json([
            'status' => 200,
            'message' => $orders
        ]);
    }

    function DeliveredOrders()
    {
        $orders = Order::searchProduct()
            ->where('status', Status::Delivered->value)
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString();



        $orders->map(function ($order) {
            $order->variants = json_decode($order->variants);
            return $order;
        });


        return response()->json([
            'status' => 200,
            'message' => $orders
        ]);
    }

    function CanceldOrders()
    {
        $orders = Order::searchProduct()
            ->where('status', Status::Cancel->value)
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString();



        $orders->map(function ($order) {
            $order->variants = json_decode($order->variants);
            return $order;
        });


        return response()->json([
            'status' => 200,
            'message' => $orders
        ]);
    }

    function updateStatus(Request $request, $id)
    {
        $order =  Order::find($id);
        if ($order) {


            if ($order->reason) {
                $order->reason = $request->reason;
            }


            if($order->status == Status::Hold->value){
               if($request->status != Status::Cancel->value){
                    $vendor = User::find($order->vendor_id);
                    if($order->product_amount > $vendor->balance){
                        return response()->json([
                            'status'=>401,
                            'message'=>'Vendor balance not available'
                        ]);
                    }else{
                        $vendor->balance = ($vendor->balance - $order->product_amount);
                        $vendor->save();
                    }
               }
            }

            if ($request->status == Status::Delivered->value) {
                $vendor = User::find($order->vendor_id);

                $balance = PendingBalance::where('order_id', $order->id)->first();
                $balance->status = Status::Success->value;
                $balance->save();

                $user =  User::find($balance->affiliator_id);
                $user->balance = ($user->balance + $balance->amount);
                $user->save();
            }

            if ($request->status == Status::Cancel->value) {



                $balance = PendingBalance::where('order_id', $order->id)->first();

                if ($order->status == Status::Delivered->value) {

                    $user =  User::find($balance->affiliator_id);
                    $user->balance = ($user->balance - $balance->amount);
                    $user->save();

                }


                if($order->status != Status::Hold->value){
                    $vendor = User::find($order->vendor_id);
                    $vendor->balance = ($vendor->balance + $balance->amount);
                    $vendor->save();
                }


                $balance->status = Status::Cancel->value;
                $balance->save();


                $product =  Product::find($order->product_id);
                $product->qty = ($product->qty + $balance->qty);

                $variants = json_decode($order->variants);
                $data = collect($variants)->pluck('qty','variant_id');


                $result = [];

                foreach ($data as $variantId => $qty) {
                    $result[] = [
                        "variant_id" => $variantId,
                        "qty" => $qty
                    ];
                }


                $databaseValues = $product->variants;
                $userValues = $result;



                if($databaseValues != ''){
                    foreach ($databaseValues as &$databaseItem) {
                        $variantId = $databaseItem['id'];
                        $matchingUserValue = collect($userValues)->firstWhere('variant_id', $variantId);

                        if ($matchingUserValue) {
                            $userQty = $matchingUserValue['qty'];
                            $databaseItem['qty'] += $userQty;
                        }
                    }

                    $product->variants = $databaseValues;
                }

                $product->save();
            }





            $order->status = $request->status;
            $order->save();

            return response()->json([
                'status' => 200,
                'message' => 'Updated Successfully!'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Not found'
            ]);
        }
    }
    function orderView($id)
    {
        $order = Order::find($id);

        if ($order) {
            //    $data =  $order->load(['product','vendor','affiliator']);
            $allData =    $order->load(['product', 'vendor', 'affiliator']);

            $allData->variants = json_decode($allData->variants);

            return response()->json([
                'status' => 200,
                'message' => $allData
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Not found'
            ]);
        }
    }

    function HoldOrders()
    {
        $orders = Order::searchProduct()
            ->where('status', Status::Hold->value)
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString();



        $orders->map(function ($order) {
            $order->variants = json_decode($order->variants);
            return $order;
        });


        return response()->json([
            'status' => 200,
            'message' => $orders
        ]);
    }
}

<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderVariant;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    function store(Request $request)
    {
        Log::info($request->datas);
        foreach ($request->datas as $data) {

            if ($data['name'] == null) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Name fild is required'
                ]);
            }

            if ($data['phone'] == null) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Phone fild is required'
                ]);
            }

            if ($data['city'] == null) {
                return response()->json([
                    'status' => 201,
                    'message' => 'City fild is required'
                ]);
            }
            if ($data['address'] == null) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Address fild is required'
                ]);
            }

            if ($data['variants'][0]['qty'] <= 0) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Quantity fild is required'
                ]);
            }


            $product = Product::find($data['product_id']);

            if($product->qty >= $data['variants'][0]['qty']){

                $product->qty = ($product->qty - $data['variants'][0]['qty']);
                $product->save();

            }else{
                return response()->json([
                    'status'=>201,
                    'message'=>'Product quantity  not available!'
                ]);
            }




            $pendingBalance = Order::where('vendor_id', $data['vendor_id'])
            ->where('status','!=',Status::Delivered->value)
            ->where('status','!=',Status::Cancel->value)
            ->where('status','!=',Status::Rejected->value)
            ->sum('afi_amount');


            $vendor_balance = User::find($data['vendor_id'])->balance - $pendingBalance;


            $afi_amount = $data['variants'][0]['qty'] * $data['amount'];

            if($vendor_balance >= $afi_amount){
                $status = Status::Pending->value;
            }else{
                $status = Status::Hold->value;
            }



            $order =   Order::create([
                'vendor_id' => $data['vendor_id'],
                'affiliator_id' => auth()->user()->id,
                'product_id' => $data['product_id'],
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'city' => $data['city'],
                'address' => $data['address'],
                'variants' => json_encode($data['variants']),
                'afi_amount' => $afi_amount,
                'product_amount' => $product->selling_price * $data['variants'][0]['qty'],
                'status' =>  $status,
            ]);

            PendingBalance::create([
                'affiliator_id'=>auth()->user()->id,
                'product_id'=> $data['product_id'],
                'order_id'=>$order->id,
                'qty'=>$data['variants'][0]['qty'],
                'amount'=>$afi_amount,
                'status'=>Status::Pending->value
            ]);

            DB::table('carts')->where('id', $data['cart_id'])->delete();
        }


        return response()->json([
            'status' => 200,
            'message' => 'Checkout successfully!'
        ]);
    }
    function pendingOrders()
    {
        $orders = Order::searchProduct()
            ->where('affiliator_id', auth()->user()->id)
            ->where('status', Status::Pending->value)
            ->with(['product:id,name','vendor:id,name'])
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
            ->where('affiliator_id',auth()->user()->id)
            ->where('status', Status::Progress->value)
            ->with(['product:id,name','vendor:id,name'])
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
            ->where('affiliator_id', auth()->user()->id)
            ->where('status',Status::Delivered->value)
            ->with(['product:id,name','vendor:id,name'])
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
            ->where('affiliator_id', auth()->user()->id)
            ->where('status', Status::Cancel->value)
            ->with(['product:id,name','vendor:id,name'])
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
    function AllOrders()
    {
        $orders = Order::searchProduct()
            ->where('affiliator_id', auth()->user()->id)
            ->with(['product:id,name','vendor:id,name','affiliator:id,name'])
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

    function orderView($id){
        $order  = Order::where('id',$id)->where('affiliator_id',auth()->user()->id)->first();
        if($order){
         $allData =    $order->load(['product','vendor','affiliator']);

            $allData->variants = json_decode($allData->variants);

            return response()->json([
                'status'=>200,
                'message'=>$allData
            ]);

        }else{
            return response()->json([
                'status'=>404,
                'message'=>'Not found'
            ]);
        }


    }
    function HoldOrders(){
        $orders = Order::searchProduct()
            ->where('affiliator_id', auth()->user()->id)
            ->where('status', Status::Hold->value)
            ->with(['product:id,name','vendor:id,name'])
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

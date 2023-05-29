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

        // return $request->datas;

        $validator =  Validator::make($request->all(),[
            'datas'=>['required','array'],
            'datas.*.name'=>['required'],
            'datas.*.phone'=>['required','integer','min:1'],
            'datas.*.email'=>['nullable'],
            'datas.*.city'=>['required'],
            'datas.*.address'=>['required'],
            'datas.*.vendor_id'=>['required','integer'],
            'datas.*.variants'=>['required','array'],
            'datas.*.variants.*.qty'=>['required','integer','min:1']
        ]);

        $validator->after(function($validator){

            $totalProductQty = Product::find(request('datas')[0]['product_id'])->qty;

            $totalQty = collect(request('datas'))->sum(function ($item) {
                return collect($item['variants'])->sum('qty');
            });

            if($totalQty > $totalProductQty){
                $validator->errors()->add('datas.*.variants.*.qty','Product quantity not available');
            }
        });


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        }

        $product = Product::find(request('datas')[0]['product_id']);

        foreach ($request->datas as $data) {


            $sumQty = 0;
            foreach ($data['variants'] as $item) {
                $sumQty += intval($item['qty']);
            }




            if ($product->qty >= $data['variants'][0]['qty']) {
                $product->qty = ($product->qty - $sumQty);
                $product->save();
            } else {
                return response()->json([
                    'status' => 201,
                    'message' => 'Product quantity  not available!'
                ]);
            }


            $result = [];
            $databaseValue = Product::find($data['product_id']);

            if ($databaseValue->variants != '') {

                foreach ($databaseValue->variants as $dbItem) {
                    foreach ($data['variants'] as $userItem) {
                        if ($dbItem['color_name'] === $userItem['color']) {
                            $dbItem['qty'] -= $userItem['qty'];
                            break;
                        }
                    }
                    $result[] = $dbItem;
                }



                $databaseValue->variants = $result;
                $databaseValue->save();
            }





            $pendingBalance = Order::where('vendor_id', $data['vendor_id'])
                ->where('status', '!=', Status::Delivered->value)
                ->where('status', '!=', Status::Cancel->value)
                ->where('status', '!=', Status::Rejected->value)
                ->sum('afi_amount');


            $vendor_balance = User::find($data['vendor_id'])->balance - $pendingBalance;


            $afi_amount = $sumQty * $data['amount'];

            if ($vendor_balance >= $afi_amount) {
                $status = Status::Pending->value;
            } else {
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
                'product_amount' => $product->selling_price * $sumQty,
                'status' =>  $status,
            ]);

            PendingBalance::create([
                'affiliator_id' => auth()->user()->id,
                'product_id' => $data['product_id'],
                'order_id' => $order->id,
                'qty' => $sumQty,
                'amount' => $afi_amount,
                'status' => Status::Pending->value
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
            ->with(['product:id,name', 'vendor:id,name'])
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
            ->where('affiliator_id', auth()->user()->id)
            ->where('status', Status::Progress->value)
            ->with(['product:id,name', 'vendor:id,name'])
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
            ->where('status', Status::Delivered->value)
            ->with(['product:id,name', 'vendor:id,name'])
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
            ->with(['product:id,name', 'vendor:id,name'])
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
            ->with(['product:id,name', 'vendor:id,name', 'affiliator:id,name'])
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

    function orderView($id)
    {
        $order  = Order::where('id', $id)->where('affiliator_id', auth()->user()->id)->first();
        if ($order) {
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
            ->where('affiliator_id', auth()->user()->id)
            ->where('status', Status::Hold->value)
            ->with(['product:id,name', 'vendor:id,name'])
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

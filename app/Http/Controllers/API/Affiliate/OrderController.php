<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
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

    function store(ProductRequest $request)
    {

        $request->validated();
        $cart  = Cart::find(request('cart_id'));

        $getmembershipdetails = getmembershipdetails();
        if ($getmembershipdetails == null) {
            return responsejson('You do not have a membership', 'fail');
        }

        if ($getmembershipdetails->expire_date <= now()) {
            return responsejson('Your membership expire', 'fail');
        }

        $product = Product::query()
            ->where(['id' => $cart->product_id, 'status' => 'active'])
            ->whereHas('vendor', function ($query) {
                $query->whereHas('vendorsubscription', function ($query) {
                    $query->where('expire_date', '>', now());
                });
            })
            ->whereHas('productdetails', function ($query) {
                $query->where(['user_id' => userid(), 'status' => 1]);
            })
            ->first();

        if (!$product) {
            return responsejson('Product not available currently!');
        }

        if($cart->purchase_type == 'single'){
            if($product->selling_type !=  'single'){
                return responsejson('Something is wrong. Delete the cart.');
            }
        }

        $datas = collect(request('datas'));



        if ($cart->purchase_type == 'bulk') {
            $firstaddress =  $datas->first();
            $variants  = collect($firstaddress)['variants'];
            $totalqty = collect($variants)->sum('qty');

            if($product->qty < $totalqty){
                return responsejson('Product quantity not available!','fail');
            }
        }

        if($cart->purchase_type == 'single'){ //single

            $varients = $datas->pluck('variants');

            $totalqty = collect($varients)->collapse()->sum('qty');

            if($product->qty < $totalqty){
                return responsejson('Product quantity not available!','fail');
            }
        }
        if ($product->status == Status::Pending->value) {
            return responsejson('The product under construction!','fail');
        }

        $uservarients = collect(request()->datas)->pluck('variants')->collapse();


        if ($product->variants != ''){
            foreach($uservarients  as $vr){
               $data = collect($product->variants)->where('id', $vr['variant_id'])->where('qty','>=',$vr['qty'])->first();
               if(!$data){
                return responsejson('Something is wrong. Delete the cart','fail');
               }
            }
        }


        // $validator =  Validator::make($request->all(), [
        //     'datas' => ['required', 'array'],
        //     'datas.*.name' => ['required'],
        //     'datas.*.phone' => ['required', 'integer', 'min:1'],
        //     'datas.*.email' => ['nullable'],
        //     'datas.*.city' => ['required'],
        //     'datas.*.address' => ['required'],
        //     'datas.*.vendor_id' => ['required', 'integer'],
        //     'datas.*.variants' => ['required', 'array'],
        //     'datas.*.variants.*.qty' => ['required', 'integer', 'min:1']
        // ]);

        // $validator->after(function ($validator) {
        //     $product = Product::find(request('datas')[0]['product_id']);
        //     $totalProductQty = Product::find(request('datas')[0]['product_id'])->qty;

        //     $totalQty = collect(request('datas'))->sum(function ($item) {
        //         return collect($item['variants'])->sum('qty');
        //     });

        //     if ($totalQty > $totalProductQty) {
        //         $validator->errors()->add('datas.*.variants.*.qty', 'Product quantity not available');
        //     }

        //     if ($product->status == Status::Pending->value) {
        //         $validator->errors()->add('datas', 'The product under construction');
        //     }
        // });

        // $getmembershipdetails = getmembershipdetails();
        // if ($getmembershipdetails == null) {
        //     return responsejson('You do not have a membership', 'fail');
        // }

        // if ($getmembershipdetails->expire_date <= now()) {
        //     return responsejson('Your membership expire', 'fail');
        // }

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 422,
        //         'errors' => $validator->messages(),
        //     ]);
        // }


        // $product = Product::find(request('datas')[0]['product_id']);
        $cartId = request('cart_id');

        $categoryId = Cart::find($cartId)->category_id;

        foreach (request('datas') as $data) {

            $product->decrement('qty',$totalqty);

            $result = [];
            $databaseValue = $product;

            if ($databaseValue->variants != '') {

                foreach ($databaseValue->variants as $dbItem) {
                    foreach ($data['variants'] as $userItem) {
                        if ($dbItem['id'] == $userItem['variant_id']) {
                            $dbItem['qty'] -= $userItem['qty'];
                            break;
                        }
                    }
                    $result[] = $dbItem;
                }

                $databaseValue->variants = $result;
                $databaseValue->save();
            }


            $vendor_balance = User::find($product->user_id);


            $afi_amount = $totalqty * $cart->amount;

            if ($vendor_balance->balance > $afi_amount) {
                $status = Status::Pending->value;
                $vendor_balance->balance = ($vendor_balance->balance - $afi_amount);
                $vendor_balance->save();
            } else {
                $status = Status::Hold->value;
            }

            $order =   Order::create([
                'vendor_id' => $product->user_id,
                'affiliator_id' => auth()->id(),
                'product_id' => $product->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'city' => $data['city'],
                'address' => $data['address'],
                'variants' => json_encode($data['variants']),
                'afi_amount' => $afi_amount,
                'product_amount' => $cart->product_price * $totalqty,
                'status' =>  $status,
                'category_id' => $categoryId,
                'qty' => $totalqty
            ]);


            PendingBalance::create([
                'affiliator_id' => auth()->user()->id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'qty' => $totalqty,
                'amount' => $afi_amount,
                'status' => Status::Pending->value
            ]);

            DB::table('carts')->where('id', request('cart_id'))->delete();
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
            // $allData =    $order->load(['product', 'vendor', 'affiliator']);

            $allData =    $order->load([
                'product.specifications',
                'product.category:id,name',
                'product.subcategory:id,name',
                'product.brand:id,name',
                'vendor',
                'affiliator'
            ]);




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

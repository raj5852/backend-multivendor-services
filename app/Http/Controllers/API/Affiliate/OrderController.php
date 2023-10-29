<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderVariant;
use App\Models\PaymentStore;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;
use App\Services\AamarPayService;
use App\Services\ProductCheckoutService;
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

        if ($cart->purchase_type == 'single') {
            if ($product->selling_type ==  'bulk') {
                return responsejson('Something is wrong delete the cart.','fail');
            }
        }

        $datas = collect(request('datas'));



        if ($cart->purchase_type == 'bulk') {
            $firstaddress =  $datas->first();
            $variants  = collect($firstaddress)['variants'];
            $totalqty = collect($variants)->sum('qty');

            if ($product->is_connect_bulk_single == 1) {
                if ($product->qty < $totalqty) {
                    return responsejson('Product quantity not available!', 'fail');
                }
            }
        }

        if ($cart->purchase_type == 'single') { //single

            $varients = $datas->pluck('variants');

            $totalqty = collect($varients)->collapse()->sum('qty');

            if ($product->qty < $totalqty) {
                return responsejson('Product quantity not available!', 'fail');
            }
        }

        if ($product->status == Status::Pending->value) {
            return responsejson('The product under construction!', 'fail');
        }

        $uservarients = collect(request()->datas)->pluck('variants')->collapse();


        if ($product->variants != '') {
            foreach ($uservarients  as $vr) {
                $data = collect($product->variants)->where('id', $vr['variant_id'])->where('qty', '>=', $vr['qty'])->first();
                if (!$data) {
                    return responsejson('Something is wrong. Delete the cart', 'fail');
                }
            }
        }
        $user = User::find(auth()->id());
        $advancepayment = $cart->advancepayment *  $totalqty;

        if (request('payment_type') == 'my-wallet') {
            if ($user->balance < $advancepayment) {
                return responsejson('You do not have enough balance.', 'fail');
            }
            $user->decrement('balance', $advancepayment);

            return  ProductCheckoutService::store($cart->id, $product->id, $totalqty, $user->id, request('datas'));
        } elseif (request('payment_type') == 'aamarpay') {
            $trx = uniqid();
            PaymentStore::create([
                'payment_gateway' => 'aamarpay',
                'trxid' => $trx,
                'status' => 'pending',
                'payment_type' => 'checkout',
                'info' => [
                    'cartid' => $cart->id,
                    'productid' => $product->id,
                    'totalqty' => $totalqty,
                    'userid' => $user->id,
                    'datas' => request('datas'),
                ],

            ]);
            $successurl = url('api/aaparpay/product-checkout-success');
            return  AamarPayService::gateway($advancepayment, $trx, 'Product Checkout', $successurl);
        }
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
                'product:id,specifications',
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

<?php

namespace App\Http\Controllers\API\Vendor;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductOrderRequest;
use App\Models\AdvancePayment;
use App\Models\CancelOrderBalance;
use App\Models\Order;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;
use App\Services\PaymentHistoryService;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    function AllOrders()
    {
        // return auth()->user()->id;
        $orders = Order::searchProduct()
            ->where('vendor_id', auth()->user()->id)
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
            ->where('vendor_id', auth()->user()->id)
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
            ->where('vendor_id', auth()->user()->id)
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
    function receivedOrders()
    {
        $orders = Order::searchProduct()
            ->where('vendor_id', auth()->user()->id)
            ->where('status', 'received')
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
            ->where('vendor_id', auth()->user()->id)
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
            ->where('vendor_id', auth()->user()->id)
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

    function productorderstatus(ProductOrderRequest $request, $id)
    {
        if (isactivemembership() != 1) {
            return responsejson('Membership Expire renew Now!');
        }
        $validatedData = $request->validated();

        return   ProductOrderService::orderStatus($validatedData, $id);

    }


    function  orderView($id)
    {
        $order  = Order::where('id', $id)->where('vendor_id', auth()->user()->id)->first();
        if ($order) {
            $allData =    $order->load([
                'product',
                'product.category:id,name',
                'product.subcategory:id,name',
                'product.brand:id,name',
                'vendor:id,uniqid'
            ]);



            $allData->variants = json_decode($allData->variants);
            if ($allData->status == 'pending' || $allData->status == 'hold' || $allData->status == 'cancel') {
                $allData->name = substr($allData->name, 0, 2) . '...';
                $allData->phone = substr($allData->phone, 0, 4) . '.....';
                $allData->email = substr($allData->email, 0, 3) . '....';
                $allData->address = substr($allData->address, 0, 3) . '....';
            }
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
            ->where('vendor_id', auth()->user()->id)
            ->where('status', Status::Hold->value)
            ->with(['affiliator:id,name', 'vendor:id,name', 'product:id,name,image'])
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

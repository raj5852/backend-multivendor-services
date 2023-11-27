<?php

namespace App\Http\Controllers\API\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductOrderRequest;
use App\Models\CancelOrderBalance;
use App\Models\Order;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    function allOrders()
    {
        if(checkpermission('all-order')){
            return $this->permissionmessage();
        }

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
        if(checkpermission('order-pending') != 1){
            return $this->permissionmessage();
        }

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
        if(checkpermission('delivery-processing') != 1){
            return $this->permissionmessage();
        }

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
    function ReceivedOrders()
    {
        if(checkpermission('order-received') != 1){
            return $this->permissionmessage();
        }

        $orders = Order::searchProduct()
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
        if(checkpermission('product-delivered') != 1){
            return $this->permissionmessage();
        }

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
        if(checkpermission('order-cancel') != 1){
            return $this->permissionmessage();
        }

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

    function updateStatus(ProductOrderRequest $request, $id)
    {

        $validatedData = $request->validated();

        return   ProductOrderService::orderStatus($validatedData, $id);

    }
    function orderView($id)
    {
        $order = Order::find($id);

        if ($order) {

            $allData =    $order->load([
                'product',
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
        if(checkpermission('order-hold') != 1){
            return $this->permissionmessage();
        }

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

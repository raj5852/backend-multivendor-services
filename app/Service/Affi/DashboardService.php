<?php

namespace App\Service\Affi;

use App\Enums\Status;
use App\Models\Order;
use App\Models\PendingBalance;
use App\Models\ProductDetails;
use Carbon\Carbon;

class DashboardService
{
    static function index()
    {
        $pendingBalance = new PendingBalance();
        $affiliator_id = auth()->user()->id;
        $today  = Carbon::today();
        $product_details = new ProductDetails();
        $order = new Order();

        $today_earning = $pendingBalance::where([
            'affiliator_id' => $affiliator_id,
            'status' => Status::Success->value
        ])->whereDate('created_at', $today)
            ->sum('amount');

        $active_product = $product_details->where([
            'user_id' => $affiliator_id,
            'status' => 1
        ])->count();

        $requested_product = $product_details->where([
            'user_id' => $affiliator_id,
            'status' => 2
        ])->count();

        $active_orders = $order->where([
            'affiliator_id' => $affiliator_id,
            'status' => Status::Delivered->value
        ])->count();


        return response()->json([
            'today_earning' => $today_earning,
            'active_product' => $active_product,
            'requested_product' => $requested_product,
            'active_orders' => $active_orders
        ]);
    }
    static  function orderVsRevenue()
    {

    }
}

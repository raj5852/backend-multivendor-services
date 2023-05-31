<?php

namespace App\Service\Vendor;

use App\Enums\Status;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{

    static function index()
    {
        $order = new Order();
        $vendorId = auth()->user()->id;
        $todayDate = Carbon::today();
        $product = new Product();

        $today_sell = $order->where([
            'vendor_id' => $vendorId,
            'status' => Status::Pending->value
        ])
            ->whereDate('created_at', $todayDate)
            ->sum('product_amount');


        $today_order = $order->where([
            'vendor_id' => $vendorId,
            'status' => Status::Pending->value
        ])
            ->whereDate('created_at', $todayDate)
            ->count();

        $active_product = $product->where([
            'user_id'=>$vendorId,
            'status'=> Status::Active->value
        ])->count();

        $pending_order = $order->where([
            'vendor_id'=>$vendorId,
            'status'=>Status::Pending->value
        ])->count();

        $active_order = $order->where([
            'vendor_id'=>$vendorId,
            'status'=>Status::Active->value
        ])->count();

        // $recent_orders = $order->where('')




    }
}

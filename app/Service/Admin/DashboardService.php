<?php

namespace App\Service\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardService
{

    static  function index()
    {

        $today_revenue = Order::whereDate('created_at', Carbon::now()->toDateString())
            ->where('status', 'pending')
            ->sum('product_amount');

        $today_order = Order::whereDate('created_at', Carbon::now()->toDateString())
            ->where('status', 'pending')
            ->count();

        $active_vendor = User::where([
            'role_as' => 2,
            'status' => 'active'
        ])->count();

        $active_afi = User::where([
            'role_as' => 2,
            'status' => 'active'
        ])->count();



    }
}

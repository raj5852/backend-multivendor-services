<?php

namespace App\Service\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardService
{

    static  function index()
    {
        $user = new User();

        $today_revenue = Order::whereDate('created_at', Carbon::now()->toDateString())
            ->where('status', 'pending')
            ->sum('product_amount');

        $today_order = Order::whereDate('created_at', Carbon::now()->toDateString())
            ->where('status', 'pending')
            ->count();

        $active_vendor = $user::where([
            'role_as' => 2,
            'status' => 'active'
        ])->count();

        $active_afi = $user::where([
            'role_as' => 2,
            'status' => 'active'
        ])->count();

        $user_activity = $user::where('last_seen', '>=', Carbon::now()->subMinutes(3))
            ->where('role_as', 2)
            ->count();

        $affi_activity = $user::where('last_seen', '>=', Carbon::now()->subMinutes(3))
            ->where('role_as', 3)
            ->count();

        // $monthly_completed_order = Order::





    }
}

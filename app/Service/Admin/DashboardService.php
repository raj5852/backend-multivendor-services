<?php

namespace App\Service\Admin;

use App\Enums\Status;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{

    static  function index()
    {
        $user = new User();
        $order = new Order();
        $currentMonth = Carbon::now()->month;



        $today_revenue = $order::whereDate('created_at', Carbon::now()->toDateString())
            ->where('status', 'pending')
            ->sum('product_amount');

        $today_order = $order::whereDate('created_at', Carbon::now()->toDateString())
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


        $monthly_completed_order = $order::whereMonth('created_at', $currentMonth)
            ->where('status', Status::Delivered->value)
            ->count();

        $monthly_cancelled_order  =  $order::whereMonth('created_at', $currentMonth)
            ->where('status', Status::Cancel->value)
            ->count();

        $monthly_progress_order  =  $order::whereMonth('created_at', $currentMonth)
            ->where('status', Status::Progress->value)
            ->count();

        $monthly_pending_order  =  $order::whereMonth('created_at', $currentMonth)
            ->where('status', Status::Pending->value)
            ->count();



        return response()->json([
            'today_revenue' => $today_revenue,
            'today_order' => $today_order,
            'active_vendor' => $active_vendor,
            'active_afi' => $active_afi,
            'user_activity' => $user_activity,
            'affi_activity' => $affi_activity,
            'monthly_completed_order' => $monthly_completed_order,
            'monthly_cancelled_order' => $monthly_cancelled_order,
            'monthly_progress_order' => $monthly_progress_order,
            'monthly_pending_order' => $monthly_pending_order,
        ]);
    }

    static function orderVsRevenue()
    {
        // daily

        $startHour = 1; // Starting hour
        $endHour = 3; // Ending hour

        $salesData = Order::selectRaw('HOUR(created_at) AS hour, SUM(product_amount) AS sales')
            ->whereDate('created_at', Carbon::today())
            ->whereBetween(DB::raw('HOUR(created_at)'), [$startHour, $endHour]) // Filter by the hour range
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->pluck('sales', 'hour');

            return $salesData;
    }
}

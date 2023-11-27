<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Service\Admin\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public $message = 0;
    function __construct()
    {
        if (!checkpermission('dashboard')) {
            $this->message = 1;
        }
    }

    function index()
    {
        if ($this->message == 1) {
            return $this->permissionmessage();
        }
        return  DashboardService::index();
    }
    function orderVsRevenue()
    {
        if ($this->message == 1) {
            return $this->permissionmessage();
        }
        return  DashboardService::orderVsRevenue();
    }
    function recentOrder()
    {
        if ($this->message == 1) {
            return $this->permissionmessage();
        }
        return  DashboardService::recentOrder();
    }
    function categoryStatus()
    {
        if ($this->message == 1) {
            return $this->permissionmessage();
        }
        return  DashboardService::categoryStatus();
    }
}

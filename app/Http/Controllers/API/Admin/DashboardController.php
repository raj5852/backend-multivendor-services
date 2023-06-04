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
    //
    function index(){

     return  DashboardService::index();

    }
    function orderVsRevenue(){
        return  DashboardService::orderVsRevenue();
    }
    function recentOrder(){
        return  DashboardService::recentOrder();

    }
    function categoryStatus(){
        return  DashboardService::categoryStatus();

    }
}

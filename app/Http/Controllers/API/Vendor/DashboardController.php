<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use App\Service\Vendor\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    function index(){
        return DashboardService::index();
    }
    function orderVsRevenue(){
        return DashboardService::orderVsRevenue();
    }
}

<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Http\Controllers\Controller;
use App\Service\Affi\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    function index(){
        return DashboardService::index();
    }
}

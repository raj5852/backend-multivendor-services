<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponListController extends Controller
{
    function index(){
        $coupon = Coupon::where('user_id',userid())->withCount('couponused')->get();
        return $this->response($coupon);
    }

}

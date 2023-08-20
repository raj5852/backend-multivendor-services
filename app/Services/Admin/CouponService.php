<?php

namespace App\Services\Admin;

use App\Models\Coupon;

/**
 * Class CouponService.
 */
class CouponService
{

    static function create($validatedData)
    {
        $coupon =  Coupon::create($validatedData);
        return $coupon;
    }
}

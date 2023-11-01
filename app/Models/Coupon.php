<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    function user(){
        return $this->belongsTo(User::class);
    }

    function couponused(){
        return $this->hasMany(CouponUsed::class,'coupon_id');
    }
}

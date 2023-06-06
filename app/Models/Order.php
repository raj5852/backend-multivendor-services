<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];


    function affiliator(){
        return $this->belongsTo(User::class,'affiliator_id','id');
    }
    function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

    function vendor(){
        return $this->belongsTo(User::class,'vendor_id','id');
    }
  static  function searchProduct(){
        return self::when(request('search'), function ($q, $search) {
            $q->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_id = 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        });
    }

}

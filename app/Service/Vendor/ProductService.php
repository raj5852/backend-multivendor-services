<?php

namespace App\Service\Vendor;

use App\Models\Product;
use App\Models\Slider;
use Illuminate\Support\Facades\Auth;

class ProductService
{

    public static  function index()
    {
        $userId = Auth::id();

        $product = Product::where('user_id', $userId)
            ->when(request('status') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when(request('status') == 'rejected', function ($q) {
                return $q->where('status', 'rejected');
            })
            ->when(request('search'),fn($q, $search)=>$q->search($search))

            ->when(request('status') == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->select('id','uniqid','image','name','selling_price','qty','status','created_at', 'discount_type', 'discount_rate')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return $product;
    }

    public static function showSLider()
    {
        $sliders = Slider::all();
        return $sliders;
    }


    public static  function countThis()
    {
        $userId = Auth::id();

        $count = Product::where('user_id', $userId)
            ->when(request('status') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when(request('status') == 'rejected', function ($q) {
                return $q->where('status', 'rejected');
            })
            ->when(request('status') == 'active', function ($q) {
                return $q->where('status', 'active');
            })->count();
        return $count;
    }

}

<?php

namespace App\Service\Vendor;

use App\Models\Product;
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
            ->when(request('search'),fn($q, $name)=>$q->where('name','like',"%{$name}%"))

            ->when(request('status') == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->with([
                'specifications',
                'category:id,name,slug,image',
                'subcategory:id,name',
                'brand:id,name',
                'colors:id,name',
                'sizes:id,name',
                'productImage',

            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();




        return $product;
    }
}

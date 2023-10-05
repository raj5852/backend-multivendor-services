<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SingleProductController extends Controller
{

    public function AffiliatorProductSinglePage($id)
    {

        $userId = Auth::id();
        $productDetails = ProductDetails::where('user_id', $userId)->with(['product' => function ($query) {
            $query->with('productImage', 'sizes', 'colors');
        }])->where('id', $id)->first();

        return response()->json([
            'status' => 200,
            'productDetails' => $productDetails,
        ]);
    }


    public function AffiliatorProductSingle($id)
    {
        // color_product , sizes
        $product = Product::with('specifications','category', 'subcategory','productImage', 'brand', 'vendor:id,name,image', 'productdetails')
        ->whereHas('productdetails',function($query){
            $query->where('user_id',auth()->id());
        })
        ->find($id);
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Product Id Found'
            ]);
        }
    }


}

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
        $product = Product::query()
            ->with('specifications', 'category', 'subcategory', 'productImage', 'brand', 'vendor:id,name,image', 'productdetails')
            ->where('status', 'active')
            ->whereHas('vendor', function ($query) {
                $query->withCount(['vendoractiveproduct' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    })
                    ->withSum('usersubscription', 'affiliate_request')
                    ->having('vendoractiveproduct_count', '<', \DB::raw('usersubscription_sum_affiliate_request'));
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

    public function AffiliatoractiveProduct($id)
    {
        $product = Product::query()
            ->with('specifications', 'category', 'subcategory', 'productImage', 'brand', 'vendor:id,name,image', 'productdetails')
            ->where('status', 'active')
            ->whereHas('vendor', function ($query) {
                $query->withCount(['vendoractiveproduct' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    })
                    ->withSum('usersubscription', 'affiliate_request')
                    ->having('vendoractiveproduct_count', '<', \DB::raw('usersubscription_sum_affiliate_request'));
            })
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

<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStatusController extends Controller
{
    public function AffiliatorProducts()
    {
        $product = Product::where('status', 'active')
            ->when(request('search'), fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))

            ->whereDoesntHave('productdetails', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }


    public function  AffiliatorProductPendingProduct()
    {
        $userId = Auth::id();
        $searchTerm = request('search');

        $pending = ProductDetails::with('product')
        ->where('user_id', $userId)
        ->where('status', 2)
        ->whereHas('product', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%'.$searchTerm.'%');
        })
        ->latest()->paginate(10)
        ->withQueryString();

        return response()->json([
            'status' => 200,
            'pending' => $pending,
        ]);
    }


    public function AffiliatorProductActiveProduct()
    {
        $userId = Auth::id();
        $searchTerm = request('search');

        $active = ProductDetails::with('product')->where('user_id', $userId)->where('status', 1)
        ->whereHas('product', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%'.$searchTerm.'%');
        })
        ->latest()->paginate(10)
        ->withQueryString();

        return response()->json([
            'status' => 200,
            'active' => $active,
        ]);
    }


    public function  AffiliatorProductRejct()
    {
        $userId = Auth::id();
        $searchTerm  = request('search');

        $reject = ProductDetails::with(['product','vendor:id,name'])->where('user_id', $userId)->where('status', 3)
        ->whereHas('product', function ($query) use ($searchTerm) {
            $query->where('name', 'like', '%'.$searchTerm.'%');
        })
        ->latest()->paginate(10)
        ->withQueryString();

        return response()->json([
            'status' => 200,
            'pending' => $reject,
        ]);
    }


    public function AffiliatorProductRequest(Request $request, $id)
    {

        $getmembershipdetails = getmembershipdetails();
        $acceptableproduct = ProductDetails::where(['user_id'=> userid(),'status'=>1])->count();

        $productecreateqty = $getmembershipdetails?->product_request;

        $totalcreatedproduct = ProductDetails::where('user_id', userid())->count();

        if (ismembershipexists() != 1) {
            return responsejson('You do not have a membership', 'fail');
        }

        if (isactivemembership() != 1) {
            return responsejson('Membership expired!', 'fail');
        }

        if ($productecreateqty <=  $totalcreatedproduct) {
            return responsejson('You can send product request more then ' . $productecreateqty . '.', 'fail');
        }

        if($getmembershipdetails ?->product_approve <= $acceptableproduct){
            return responsejson('You product accept limit over.', 'fail');
        }


        $product = new ProductDetails();
        $product->status = 2;
        $product->product_id = $request->product_id;
        $product->vendor_id = $request->vendor_id;
        $product->user_id = Auth::user()->id;
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product Request Successfully Please Wait',
        ]);
    }




}

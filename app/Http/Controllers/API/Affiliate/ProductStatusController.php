<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductStatusController extends Controller
{
    public function AffiliatorProducts()
    {
        $product = Product::query()
            ->where('status', 'active')
            ->when(request('search'), fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))
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
            ->whereHas('product')
            ->when($searchTerm != '', function ($query) use ($searchTerm) {
                $query->whereHas('product', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->paginate(10)
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

        $active = ProductDetails::query()
            ->with('product')
            ->where(['user_id' => $userId, 'status' => 1])
            ->whereHas('product')
            ->when($searchTerm != '', function ($query) use ($searchTerm) {
                $query->whereHas('product', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $searchTerm . '%');
            })
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
            ->latest()->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'active' => $active,
        ]);
    }

    function vendorexpireproducts()
    {
        $userId = Auth::id();
        $searchTerm = request('search');

        $active = ProductDetails::with('product')->where('user_id', $userId)->where('status', 1)
            ->whereHas('product')
            ->when($searchTerm != '', function ($query) use ($searchTerm) {
                $query->whereHas('product', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $searchTerm . '%');
            })
            ->whereHas('vendor', function ($query) {
                $query->withCount(['vendoractiveproduct' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '<', now());
                    });
            })
            ->latest()
            ->paginate(10)
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

        $reject = ProductDetails::query()
            ->with(['product', 'vendor:id,name'])
            ->where(['user_id' => $userId, 'status' => 3])
            ->whereHas('product')
            ->when($searchTerm != '', function ($query) use ($searchTerm) {
                $query->whereHas('product', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'pending' => $reject,
        ]);
    }


    public function AffiliatorProductRequest(Request $request, $id)
    {

        $getmembershipdetails = getmembershipdetails();
        $acceptableproduct = ProductDetails::where(['user_id' => userid(), 'status' => 1])->count();

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

        if ($getmembershipdetails?->product_approve <= $acceptableproduct) {
            return responsejson('You product accept limit over.', 'fail');
        }

        $existproduct = Product::query()
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
            ->find(request('product_id'));

        if (!$existproduct) {
            return $this->response('Product not fount');
        }

        $product = new ProductDetails();
        $product->status = 2;
        $product->product_id = $existproduct->id;
        $product->vendor_id = $existproduct->user_id;
        $product->user_id = auth()->id();
        $product->reason = request('reason');
        $product->uniqid = uniqid();
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product Request Successfully Please Wait',
        ]);
    }
}

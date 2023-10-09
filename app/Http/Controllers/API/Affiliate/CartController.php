<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductDetails;
use App\Models\User;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function addtocart(Request $request)
    {

        $getproduct = Product::query()
            ->where('id', $request->product_id)
            ->whereHas('productdetails', function ($query) {
                $query->where([
                    'user_id' => auth()->id(),
                    'status'=> 1
                ]);
            })
            ->where('status', 'active')
            ->first();

        if (!$getproduct) {
            return responsejson('Product not found!');
        }












        $user_id = userid();
        $product_id = $getproduct->id;
        $product_price = $getproduct->selling_price;
        $vendor_id = $getproduct->user_id;

        $productAmount = $getproduct->selling_price;



        if ($request->discount_type == 'flat') {
            $amount = $getproduct->discount_rate;
        }

        if ($request->discount_type == 'percent') {
            $amount = ($productAmount / 100) * $getproduct->discount_rate;
        }



        if (Cart::where('product_id', $product_id)->where('user_id', $user_id)->exists()) {
            return response()->json([
                'status' => 409,
                'message' => $getproduct->name . ' already added to cart.',
            ]);
        }


        $cartitem = new Cart();
        $cartitem->user_id = $user_id;
        $cartitem->product_id = $product_id;
        $cartitem->product_price = $product_price;
        $cartitem->vendor_id = $vendor_id;
        $cartitem->amount = $amount;
        $cartitem->category_id = $getproduct->category_id;

        $cartitem->save();

        $colors = [];
        $sizes = [];
        $qnts = [];
        $variants = [];


        foreach ($request->cartItems  as $data) {
            $colors[] = $data['color'] ?? null;
            $sizes[] = $data['size'] ?? null;
            $qnts[] = $data['qty'];
            $variants[] = $data['id'] ?? null;
        }


        foreach ($qnts as $key => $value) {
            CartDetails::create([
                'cart_id' => $cartitem->id,
                'color' => $colors[$key],
                'size' => $sizes[$key],
                'qty' => $value,
                'variant_id' => $variants[$key]
            ]);
        }

        return response()->json([
            'status' => 201,
            'message' => 'Added to Cart',
        ]);


        return response()->json([
            'status' => 404,
            'message' => 'Product Not Found',
        ]);
    }

    public function viewcart()
    {

        $user_id = auth()->user()->id;
        $cartitems = Cart::where('user_id', $user_id)->with(['cartDetails', 'product:id,name'])

            ->whereHas('product', function ($query) {
                $query->whereHas('productdetails', function ($query) {
                    $query->where('status', 1);
                })
                    ->whereHas('vendor', function ($query) {
                        $query->whereHas('usersubscription', function ($query) {
                            $query->where('expire_date', '>', now());
                        });
                    });
            })
            ->get();

        return response()->json([
            'status' => 200,
            'cart' => $cartitems,
        ]);
    }

    public function deleteCartitem($cart_id)
    {

        $cartitem = Cart::where('id', $cart_id)->where('user_id', userid())->first();
        if ($cartitem) {
            $cartitem->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Cart Item Removed Successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Cart Item not Found',
            ]);
        }
    }

    public function updatequantity()
    {
        echo "DOne";
    }



    function affiliatorCart($id)
    {
        $cart = Cart::where('user_id', userid())
            ->whereHas('product', function ($query) {
                $query->whereHas('vendor', function ($query) {
                    $query->whereHas('vendorsubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    });
                });
            })
            ->find($id);

        if (!$cart) {
            return response()->json([
                'status' => 'Not found'
            ]);
        } else {
            $data = Cart::find($id)->load(['product:id,name', 'cartDetails']);
            return response()->json($data);
        }
    }
}

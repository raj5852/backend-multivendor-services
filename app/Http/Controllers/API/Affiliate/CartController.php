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
        // Log::info($request->all());

        $user_id = auth()->user()->id;
        $product_id = $request->product_id;
        $product_price = $request->product_price;
        $vendor_id = $request->vendor_id;

        $productAmount = Product::find($request->product_id)->selling_price;



        if ($request->discount_type == 'flat') {
            $amount = $request->discount_rate;
        }

        if ($request->discount_type == 'percent') {
            $amount = ($productAmount / 100) * $request->discount_rate;
        }


        $productCheck = Product::where('id', $product_id)->first();
        if ($productCheck) {
            if (Cart::where('product_id', $product_id)->where('user_id', $user_id)->exists()) {
                return response()->json([
                    'status' => 409,
                    'message' => $productCheck->name . ' Already Added to Cart',
                ]);
            } else {
                $cartitem = new Cart();
                $cartitem->user_id = $user_id;
                $cartitem->product_id = $product_id;
                $cartitem->product_price = $product_price;
                $cartitem->vendor_id = $vendor_id;
                $cartitem->amount = $amount;
                $cartitem->category_id = $request->category_id;

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
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Product Not Found',
            ]);
        }
    }

    public function viewcart()
    {
        if (auth('sanctum')->check()) {

            $user_id = auth()->user()->id;
            $cartitems = Cart::where('user_id', $user_id)->with(['cartDetails', 'product:id,name'])
                ->whereHas('product', function ($query) {
                    $query->whereHas('vendor', function ($query) {
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
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to View Cart Data',
            ]);
        }
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
        $cart = Cart::where('user_id',userid())
        ->whereHas('product',function($query){
            $query->whereHas('vendor',function($query){
                $query->whereHas('vendorsubscription',function($query){
                    $query->where('expire_date','>',now());
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

<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductDetails;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{

    public function AffiliatorProfile()
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'status' => 200,
            'user' => $user
        ]);
    }
    function affiliatorCart($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json([
                'status' => 'Not found'
            ]);
        } else {
            $data = Cart::find($id)->load(['product:id,name', 'cartDetails']);
            return response()->json($data);
        }
    }

    public function AffiliatorUpdateProfile(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required|integer',
            'number2' => 'nullable|integer',
            'old_password' => 'nullable',
            'new_password' => 'nullable',
        ]);

        if ($request->has('old_password') && $request->input('old_password') !== null) {
            $validator->addRules([
                'new_password' => 'required|min:8|max:32',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->messages()
            ]);
        }


        $data = User::find(Auth::user()->id);
        $data->name = $request->name;
        $data->number = $request->number;
        $data->number2 = $request->number2;
        if ($request->old_password) {
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Old Password Not Match!'
                ]);
            } else {
                $data->password = bcrypt($request->new_password);
            }
        }


        if ($request->hasFile('image')) {
            if (File::exists($data->image)) {
                File::delete($data->image);
            }
            $image =  fileUpload($request->image, 'uploads/affiliator');


            $data->image = $image;
        }

        $data->save();
        return response()->json([
            'status' => 200,
            'message' => 'Profile updated Sucessfully',
        ]);
    }




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

    public function AffiliatorProductSingle($id)
    {
        $product = Product::with('category', 'subcategory', 'colors', 'sizes', 'productImage', 'brand', 'vendor:id,name,email,role_as,image,number,number2,status,created_at,updated_at', 'productdetails')->find($id);
        if ($product) {
            // $product->variants = json_decode($product->variants);

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

    public function AffiliatorProductRequest(Request $request, $id)
    {
        // $product=ProductDetails::find($id);
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


    public function  AffiliatorProductPendingProduct()
    {
        $userId = Auth::id();
        $pending = ProductDetails::with('product')->where('user_id', $userId)->where('status', 2)->latest()->paginate(10);
        return response()->json([
            'status' => 200,
            'pending' => $pending,
        ]);
    }

    public function AffiliatorProductActiveProduct()
    {
        $userId = Auth::id();
        $active = ProductDetails::with('product')->where('user_id', $userId)->where('status', 1)->latest()->paginate(10);
        return response()->json([
            'status' => 200,
            'active' => $active,
        ]);
    }

    public function  AffiliatorProductRejct()
    {
        $userId = Auth::id();
        $reject = ProductDetails::with(['product','vendor:id,name'])->where('user_id', $userId)->where('status', 3)->latest()->paginate(10);
        return response()->json([
            'status' => 200,
            'pending' => $reject,
        ]);
    }



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



}

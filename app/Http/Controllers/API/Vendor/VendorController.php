<?php

namespace App\Http\Controllers\API\Vendor;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\User;
use App\Models\Subcategory;
use App\Models\ProductDetails;
use App\Models\specification;
use App\Service\Vendor\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Image;

class VendorController extends Controller
{
    public function VendorProfile()
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'status' => 200,
            'user' => $user
        ]);
    }

    public function VendorUpdateProfile(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required|integer',
            'number2'=>'nullable|integer',
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
            }else{
                $data->password = bcrypt($request->new_password);
            }
        }


        if ($request->hasFile('image')) {
            if (File::exists($data->image)) {
                File::delete($data->image);
            }
            $image =  fileUpload($request->image, 'uploads/vendor');
            $data->image = $image;
        }

        $data->save();
        return response()->json([
            'status' => 200,
            'message' => 'Profile updated Sucessfully!',
        ]);
    }


    public function VendorProduct()
    {
        return response()->json([
            'status' => 200,
            'product' => ProductService::index(),
        ]);
    }



    public function VendorProductStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required',
            'qty' => 'required',
            'selling_price' => 'required',
            'original_price' => 'required',
            'brand_id' => 'required',
            'image' => 'required',
            'images' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $product = new Product;
            $product->category_id = $request->category_id;
            $product->subcategory_id = $request->subcategory_id;
            $product->brand_id = $request->brand_id;
            $product->user_id = Auth::user()->id;
            $product->name = $request->name;
            $product->slug =   slugCreate(Product::class, $request->name);
            $product->short_description = $request->short_description;
            $product->long_description = $request->long_description;
            $product->selling_price = $request->selling_price;
            $product->original_price = $request->original_price;
            $product->qty = $request->qty;
            $product->status = Status::Pending->value;
            $product->meta_title = $request->meta_title;
            $product->meta_keyword = $request->meta_keyword;
            $product->meta_description = $request->meta_description;

            $product->tags  = $request->tags; //array
            $product->discount_type = $request->discount_type;
            $product->discount_rate  = $request->discount_rate;
            $product->variants = $request->variants; //array


            if ($request->hasFile('image')) {
                $filename =   fileUpload($request->file('image'), 'uploads/product',500,500);
                $product->image =  $filename;
            }

            $product->save();

            if ($request->specification) {
                foreach ($request->specification as $key => $sp) {
                    specification::create([
                        'product_id' => $product->id,
                        'specification' => $sp,
                        'specification_ans' => $request->specification_ans[$key]
                    ]);
                }
            }

            $productId = $product->id;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageUrl =   fileUpload($image, 'uploads/product');
                    $proimage = new ProductImage();
                    $proimage->product_id = $productId;
                    $proimage->image = $imageUrl;
                    $proimage->save();
                }
            }


            return response()->json([
                'status' => 200,
                'message' => 'Product Added Sucessfully',
            ]);
        }
    }

    public function VendorProductEdit($id)
    {
        $userId = Auth::id();
        $product = Product::with('vendor','brand','specifications', 'category', 'subcategory', 'colors', 'sizes', 'productImage')->where('user_id', $userId)->find($id);

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



    public function VendotUpdateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required',
            'qty' => 'required',
            'selling_price' => 'required',
            'original_price' => 'required',
            'brand_id' => 'required',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $product = Product::find($id);
            if ($product) {

                $product->category_id = $request->input('category_id');
                $product->subcategory_id = $request->input('subcategory_id');
                $product->brand_id = $request->input('brand_id');
                $product->user_id = auth()->user()->id;

                $product->name = $request->input('name');
                $product->slug = slugCreate(Product::class, $request->name);
                $product->short_description = $request->input('short_description');
                $product->long_description = $request->input('long_description');
                $product->selling_price = $request->input('selling_price');
                $product->original_price = $request->input('original_price');
                $product->qty = $request->input('qty');
                // $product->status = $request->input('status');
                $product->meta_title = $request->input('meta_title');
                $product->meta_keyword = $request->input('meta_keyword');
                $product->meta_description = $request->input('meta_description');

                $product->tags = $request->input('tags');
                $product->discount_type = $request->discount_type;
                $product->discount_rate  = $request->discount_rate;
                $product->variants = $request->variants;

                $product->update();
                // $product->colors()->sync($request->colors);
                // $product->sizes()->sync($request->sizes);

                DB::table('specifications')->where('product_id', $product->id)->delete();

                if ($request->specification) {
                    foreach ($request->specification as $key => $sp) {
                        specification::create([
                            'product_id' => $product->id,
                            'specification' => $sp,
                            'specification_ans' => $request->specification_ans[$key]
                        ]);
                    }
                }


                if ($request->hasFile('image')) {
                    $path = $product->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    fileUpload($request->file('image'),'uploads/product');
                }

                $productId = $product->id;
                $update_images = ProductImage::where('product_id', $productId)->get();
                $images = $request->file('images');

                if ($images) {
                    foreach ($images as $key => $image) {
                        // image01 upload
                        $name =  time() . $key . '-' . $image->getClientOriginalName();
                        $uploadpath = 'uploads/product/';
                        $image->move($uploadpath, $name);
                        $imageUrl = $uploadpath . $name;

                        $proimage = new ProductImage();
                        $proimage->product_id = $productId;
                        $proimage->image = $imageUrl;
                        $proimage->save();
                    }
                } else {
                    foreach ($update_images as $update_image) {
                        $uimage = $update_image->image;
                        $update_image->image  = $uimage;
                        $update_image->save();
                    }
                }



                $product->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Product Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product Not Found',
                ]);
            }
        }
    }

    public function VendorDelete($id)
    {
        $userId = Auth::id();
        $product = Product::where('user_id', $userId)->find($id);
        if ($product) {
            $product->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Product Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Product ID Found',
            ]);
        }
    }





    public function AllCategory()
    {
        $category = Category::where('status','active')->with(['subcategory'])->get();
        return response()->json([
            'status' => 200,
            'category' => $category,
        ]);
    }

    public function AllSubCategory()
    {
        $subcategory = Subcategory::where('status','active')->get();
        return response()->json([
            'status' => 200,
            'subcategory' => $subcategory,
        ]);
    }


    // public function AllBrand()
    // {
    //   $brand= Brand::where('status','active')->get();
    //   return response()->json([
    //       'status'=>200,
    //       'brand'=>$brand,
    //   ]);
    // }


    public function AllColor()
    {
        $color = Color::all();
        return response()->json([
            'status' => 200,
            'color' => $color,
        ]);
    }

    public function AllSize()
    {
        $size = Size::all();
        return response()->json([
            'status' => 200,
            'size' => $size,
        ]);
    }


    public function VendorBalanceRequest()
    {
        $vendor = User::find(Auth::user()->id);
        return response()->json([
            'status' => 200,
            'vendor' => $vendor
        ]);
    }

    public function VendorRequestSent(Request $request)
    {

        $data = User::find(Auth::user()->id);
        $data->balance = $request->balance;
        $data->message = $request->message;
        $data->balance_status = 0;
        $data->update();

        return response()->json([
            'status' => 200,
            'message' => 'Vendor Balance Added  Successfully Please Wait',
        ]);
    }


    public function approval($id)
    {
        $product = ProductDetails::find($id);
        if ($product->status == '2') {
            $product->status = '1';
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product is Accepted ',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Product is Request Accepted',
            ]);
        }
    }

    public function reject($id)
    {
        $product = ProductDetails::find($id);
        if ($product->status == '2') {
            $product->status = '3';
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product is Reject ',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Product is Request Accepted',
            ]);
        }
    }


    public function Accepted($id)
    {
        $product = ProductDetails::find($id);
        if ($product->status == '2') {
            $product->status = '1';
            $product->save();
            return response()->json([
                'status' => 200,
                'message' => 'Product is active ',
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Product is Inactive',
            ]);
        }
    }

    function VendorDeleteImage($id)
    {
        $image = ProductImage::find($id);
        if ($image) {
            if (File::exists($image->image)) {
                File::delete($image->image);
            }
            $image->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Product Image Deleted Successfilly!',
            ]);
        }
    }
}

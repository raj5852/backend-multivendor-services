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
use App\Rules\BrandRule;
use App\Rules\CategoryRule;
use App\Rules\SubCategorydRule;
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
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => ['required', 'integer', 'min:1', new CategoryRule],
            'subcategory_id' => ['nullable', new SubCategorydRule],
            'qty' => ['required', 'integer', 'min:1'],
            'selling_price' => ['required', 'numeric', 'min:1'],
            'original_price' => ['required', 'numeric', 'min:1'],
            'brand_id' => ['required', 'integer', 'min:1', new BrandRule],
            'discount_type' => ['nullable', 'in:percent,flat'],
            'discount_rate' => ['required_with:discount_type', 'numeric', 'min:1'],
            'meta_keyword' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'variants' => ['nullable', 'array'],
            // 'variants.*.size_name' => ['required_with:variants'],
            // 'variants.*.color_name' => ['required_with:variants'],
            'variants.*.qty' => ['required_with:variants','integer','min:0'],

            'image'=>['required','mimes:jpeg,png,jpg'],
            'images.*'=>['required','mimes:jpeg,png,jpg'],

        ]);
        $validator->after(function ($validator) {
            $discount_type = request('discount_type');
            $discount_rate = request('discount_rate');

            if($discount_type == 'flat'){
                $required_balance =  $discount_rate;
            }

            if($discount_type == 'percent'){

                $required_balance =  (request('selling_price')/100) * $discount_rate;
            }

            if ($required_balance > auth()->user()->balance) {
                $validator->errors()->add('selling_price', 'At least one product should have  a commission balance');
            }

        });




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
            $product->meta_keyword = $request->meta_keyword; //array
            $product->meta_description = $request->meta_description;

            $product->tags  = $request->tags; //array
            $product->discount_type = $request->discount_type;
            $product->discount_rate  = $request->discount_rate;
            $product->variants = $request->variants; //array


            if ($request->hasFile('image')) {
                $filename =   fileUpload($request->file('image'), 'uploads/product', 500, 500);
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
        $product = Product::with('vendor', 'brand', 'specifications', 'category', 'subcategory', 'colors', 'sizes', 'productImage')->where('user_id', $userId)->find($id);

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
        Log::info($request->all());
        $validator = Validator::make($request->all(), [


            'name' => 'required|max:255',
            'category_id' => ['required', 'integer', 'min:1', new CategoryRule],
            'subcategory_id' => ['nullable', new SubCategorydRule],
            'qty' => ['required', 'integer', 'min:1'],
            'selling_price' => ['required', 'numeric', 'min:1'],
            'original_price' => ['required', 'numeric', 'min:1'],
            'brand_id' => ['required', 'integer', 'min:1', new BrandRule],
            'discount_type' => ['nullable', 'in:percent,flat'],
            'discount_rate' => ['required_with:discount_type', 'numeric', 'min:1'],
            'meta_keyword' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'variants' => ['nullable', 'array'],
            // 'variants.*.size_name' => ['required_with:variants'],
            // 'variants.*.color_name' => ['required_with:variants'],
            'variants.*.qty' => ['required_with:variants','integer','min:0'],

            'image'=>['nullable','mimes:jpeg,png,jpg'],
            'images.*'=>['nullable','mimes:jpeg,png,jpg'],


        ]);
        $validator->after(function ($validator) {
            $discount_type = request('discount_type');
            $discount_rate = request('discount_rate');

            if($discount_type == 'flat'){
                $required_balance =  $discount_rate;
            }

            if($discount_type == 'percent'){

                $required_balance =  (request('selling_price')/100) * $discount_rate;
            }

            if ($required_balance > auth()->user()->balance) {
                $validator->errors()->add('selling_price', 'At least one product should have  a commission balance');
            }

        });

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
                $product->slug =  slugUpdate(Product::class,$request->name,$id);
                $product->short_description = $request->input('short_description');
                $product->long_description = $request->input('long_description');
                $product->selling_price = $request->input('selling_price');
                $product->original_price = $request->input('original_price');
                $product->qty = $request->input('qty');
                // $product->status = $request->input('status');
                $product->meta_title = $request->input('meta_title');
                $product->meta_keyword = $request->input('meta_keyword');
                $product->meta_description = $request->input('meta_description');
                $product->status = Status::Pending->value;
                $product->tags = $request->input('tags');
                $product->discount_type = $request->discount_type;
                $product->discount_rate  = $request->discount_rate;
                $product->variants = $request->variants;

                $product->update();


                DB::table('specifications')->where('product_id', $product->id)->delete();

                if ($request->specifications) {
                    foreach ($request->specifications as $key => $sp) {
                        specification::create([
                            'product_id' => $product->id,
                            'specification' => $sp['specification'],
                            'specification_ans' =>  $sp['specification_ans']
                        ]);
                    }
                }


                if ($request->hasFile('image')) {
                    $path = $product->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    $filename =   fileUpload($request->file('image'), 'uploads/product');
                    $product->image =  $filename;
                }



                if ($request->file('images')) {
                    foreach ($request->file('images') as $key => $image) {
                        $imageUrl = fileUpload($request->file('images')[$key],'uploads/product');
                        $proimage = new ProductImage();
                        $proimage->product_id = $product->id;
                        $proimage->image = $imageUrl;
                        $proimage->save();

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
        $category = Category::where('status', 'active')->with(['subcategory'])->get();
        return response()->json([
            'status' => 200,
            'category' => $category,
        ]);
    }

    public function AllSubCategory()
    {
        $subcategory = Subcategory::where('status', 'active')->get();
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

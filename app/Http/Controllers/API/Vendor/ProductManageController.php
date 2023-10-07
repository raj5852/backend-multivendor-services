<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\specification;
use App\Rules\BrandRule;
use App\Rules\CategoryRule;
use App\Rules\SubCategorydRule;
use App\Service\Vendor\ProductService;
use App\Services\Vendor\VendorProductValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductManageController extends Controller
{
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
            'variants.*.qty' => ['required_with:variants', 'integer', 'min:0'],
            'image' => ['required', 'mimes:jpeg,png,jpg'],
            'images.*' => ['required', 'mimes:jpeg,png,jpg'],
            'selling_type'=>['required',Rule::in(['single','bulk','both'])],
            'min_bulk_qty'=>['required_if:selling_type,bulk,both','integer','min:1'],
            'min_bulk_price'=>['required_if:selling_type,bulk,both','numeric','min:1']

        ]);

        $validator->after(function ($validator) {
            $discount_type = request('discount_type');
            $discount_rate = request('discount_rate');
            $required_balance = "";

            if ($discount_type == 'flat') {
                $required_balance =  $discount_rate;
            }

            if ($discount_type == 'percent') {
                $required_balance =  (request('selling_price') / 100) * $discount_rate;
            }

            if ($required_balance != '') {
                if ($required_balance > convertfloat(auth()->user()->balance)) {
                    $validator->errors()->add('selling_price', 'At least one product should have  a commission balance');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {

            $getmembershipdetails = getmembershipdetails();

            $productecreateqty = $getmembershipdetails?->product_qty;

            $totalcreatedproduct = Product::where('user_id', userid())->count();

            if (ismembershipexists() != 1) {
                return responsejson('You do not have a membership', 'fail');
            }

            if (isactivemembership() != 1) {
                return responsejson('Membership expired!', 'fail');
            }

            if ($productecreateqty <=  $totalcreatedproduct) {
                return responsejson('You can not create product more than ' . $productecreateqty . '.', 'fail');
            }


            $product = new Product();
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
            $product->selling_type = request('selling_type');
            $product->min_bulk_qty = request('min_bulk_qty');
            $product->min_bulk_price = request('min_bulk_price');

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
        $product = Product::with('vendor', 'brand', 'specifications', 'category', 'subcategory', 'productImage')->where('user_id', $userId)->find($id);

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
            'variants.*.qty' => ['required_with:variants', 'integer', 'min:0'],

            'image' => ['nullable', 'mimes:jpeg,png,jpg'],
            'images.*' => ['nullable', 'mimes:jpeg,png,jpg'],
            'selling_type'=>['required',Rule::in(['single','bulk','both'])],
            'min_bulk_qty'=>['required_if:selling_type,bulk,both','integer','min:1'],
            'min_bulk_price'=>['required_if:selling_type,bulk,both','numeric','min:1']

        ]);
        $validator->after(function ($validator) {
            $discount_type = request('discount_type');
            $discount_rate = request('discount_rate');

            if ($discount_type == 'flat') {
                $required_balance =  $discount_rate;
            }

            if ($discount_type == 'percent') {

                $required_balance =  (request('selling_price') / 100) * $discount_rate;
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
                $product->slug =  slugUpdate(Product::class, $request->name, $id);
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
                $product->selling_type = request('selling_type');
                $product->min_bulk_qty = request('min_bulk_qty');
                $product->min_bulk_price = request('min_bulk_price');
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
                        $imageUrl = fileUpload($request->file('images')[$key], 'uploads/product');
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

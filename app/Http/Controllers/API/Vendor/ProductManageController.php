<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Enums\Status;
use App\Models\Brand;
use App\Models\Product;
use App\Rules\BrandRule;
use App\Rules\CategoryRule;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\specification;
use App\Rules\SubCategorydRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PendingProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Service\Vendor\ProductService;
use Illuminate\Support\Facades\Validator;
use App\Services\Vendor\VendorProductValidation;

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

        // $request->validate([
        //     'd'=>'required_if:dsdd'
        // ])

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => ['required', 'integer', 'min:1', new CategoryRule],
            'subcategory_id' => ['nullable', new SubCategorydRule],
            'qty' => ['required', 'integer', 'min:1'],
            'selling_price' => ['required', 'numeric', 'min:1'],
            'original_price' => ['required', 'numeric', 'min:1'],
            'brand_id' => ['required', 'integer', 'min:1', new BrandRule],

            'meta_keyword' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'variants' => ['nullable', 'array'],

            'variants.*.qty' => ['required_with:variants', 'integer', 'min:0'],
            'image' => ['required', 'mimes:jpeg,png,jpg'],
            'images.*' => ['required', 'mimes:jpeg,png,jpg'],

            'selling_type' => ['required', Rule::in(['single', 'bulk', 'both'])],
            'advance_payment' => ['numeric', 'min:0', 'nullable'],
            'single_advance_payment_type' => [Rule::in(['flat', 'percent']), Rule::requiredIf(function () {
                return (request('advance_payment') > 0) && (in_array(request('selling_type'), ['single', 'both']));
            })],

            'selling_details' => ['required_if:selling_type,bulk,both', 'array'],
            'selling_details.*.min_bulk_qty' => ['required', 'integer', 'min:0'],
            'selling_details.*.min_bulk_price' => ['required', 'numeric', 'min:1'],
            'selling_details.*.bulk_commission' => ['numeric', 'min:0'],
            'selling_details.*.bulk_commission_type' => [Rule::in(['percent', 'flat']), 'required'],
            'selling_details.*.advance_payment' => ['present', 'numeric', 'min:0'],
            'selling_details.*.advance_payment_type' => [Rule::in(['percent', 'flat']), 'required'],

            'discount_rate' => ['required_if:selling_type,single,both', 'numeric', 'min:0'],
            'discount_type' => ['in:percent,flat', Rule::requiredIf(function () {
                return request('discount_rate') > 0;
            })],
            'is_connect_bulk_single' => [function ($attribute, $value, $fail) {
                if ((request('is_connect_bulk_single') == 1) && (request('selling_details') == 'single')) {
                    $fail('You many not active when selling type single.');
                }
            }]
        ]);


        if (request('selling_type') == 'single') {
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
        }


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
            $product->selling_details = request('selling_details');
            $product->advance_payment = request('advance_payment');
            $product->single_advance_payment_type = request('single_advance_payment_type');
            $product->is_connect_bulk_single = request('is_connect_bulk_single');

            if ($request->hasFile('image')) {
                $filename =   fileUpload($request->file('image'), 'uploads/product', 500, 500);
                $product->image =  $filename;
            }

            $specification = request('specification');
            $specification_ans  = request('specification_ans');

            $specificationdata = collect($specification)->map(function ($item, $key) use ($specification_ans) {
                return [
                    "specification" => $item,
                    "specification_ans" => $specification_ans[$key],
                ];
            })->toArray();

            $product->specifications = $specificationdata;
            $product->save();


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
        $product = Product::with('vendor', 'brand', 'category', 'subcategory', 'productImage')->where('user_id', $userId)->find($id);

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
            'category_id' => ['required', 'integer', 'min:1', new CategoryRule],
            'subcategory_id' => ['nullable', new SubCategorydRule],
            'qty' => ['required', 'integer', 'min:1'],
            'selling_price' => ['required', 'numeric', 'min:1'],
            'original_price' => ['required', 'numeric', 'min:1'],
            'brand_id' => ['required', 'integer', 'min:1', new BrandRule],
            'meta_keyword' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'variants' => ['nullable', 'array'],
            'variants.*.qty' => ['required_with:variants', 'integer', 'min:0'],
            'image' => ['nullable', 'mimes:jpeg,png,jpg'],
            'images.*' => ['nullable', 'mimes:jpeg,png,jpg'],

            'selling_type' => ['required', Rule::in(['single', 'bulk', 'both'])],
            'advance_payment' => ['numeric', 'min:0', 'nullable'],
            'single_advance_payment_type' => [Rule::in(['flat', 'percent']), Rule::requiredIf(function () {
                return (request('advance_payment') > 0) && (in_array(request('selling_type'), ['single', 'both']));
            })],

            'selling_details' => ['required_if:selling_type,bulk,both', 'array'],
            'selling_details.*.min_bulk_qty' => ['required', 'integer', 'min:0'],
            'selling_details.*.min_bulk_price' => ['required', 'numeric', 'min:1'],
            'selling_details.*.bulk_commission' => ['numeric', 'min:0'],
            'selling_details.*.bulk_commission_type' => [Rule::in(['percent', 'flat']), 'required'],
            'selling_details.*.advance_payment' => ['present', 'numeric', 'min:0'],
            'selling_details.*.advance_payment_type' => [Rule::in(['percent', 'flat']), 'required'],

            'discount_rate' => ['required_if:selling_type,single,both', 'numeric', 'min:0'],
            'discount_type' => ['in:percent,flat', Rule::requiredIf(function () {
                return request('discount_rate') > 0;
            })],
            'is_connect_bulk_single' => [function ($attribute, $value, $fail) {
                if ((request('is_connect_bulk_single') == 1) && (request('selling_details') == 'single')) {
                    $fail('You many not active when selling type single.');
                }
            }]

        ]);

        if (request('selling_type') == 'single') {
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
        }

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
                $product->user_id = auth()->id();

                $product->name = $request->input('name');
                $product->slug =  slugUpdate(Product::class, $request->name, $id);
                // $product->short_description = $request->input('short_description');
                // $product->long_description = $request->input('long_description');
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
                $product->selling_type = request('selling_type');
                $product->selling_details = request('selling_details');
                $product->advance_payment = request('advance_payment');
                $product->single_advance_payment_type = request('single_advance_payment_type');
                $product->is_connect_bulk_single = request('is_connect_bulk_single');

                $product->update();

                $specification = request('specification',[]);
                $specification_ans  = request('specification_ans',[]);

                $specificationdata = collect($specification)->map(function ($item, $key) use ($specification_ans) {
                    return [
                        "specification" => $item,
                        "specification_ans" => $specification_ans[$key],
                    ];
                })->toArray();

                info($specificationdata);

                if (($product->short_description != request('short_description')) || ($product->long_description != request('long_description'))  || request()->hasFile('image') || request()->hasFile('images') || $product->specifications != $specificationdata) {
                    $pendingproductdetails =  PendingProduct::where('product_id', $product->id)->first();

                    if (!$pendingproductdetails) {
                        $pendingproduct = new  PendingProduct();
                    } else {
                        $pendingproduct =  $pendingproductdetails;
                    }

                    $pendingproduct->product_id = $product->id;
                    if($product->short_description != request('short_description')){
                        $pendingproduct->short_description = request('short_description');
                    }

                    if($product->long_description != request('long_description')){
                        $pendingproduct->long_description = request('long_description');
                    }

                    if (request()->hasFile('image')) {
                        $pendingproduct->image =  fileUpload($request->file('image'), 'uploads/product');
                    }

                    $allimages = [];
                    if ($request->file('images')) {
                        foreach ($request->file('images') as $key => $image) {
                            $allimages[] = fileUpload($request->file('images')[$key], 'uploads/product');
                        }
                    }

                    if($product->specifications != $specificationdata){
                        $pendingproduct->specifications =  $specificationdata;
                    }


                    if (request()->has('images')) {
                        $pendingproduct->images =  $allimages;
                    }
                    $pendingproduct->save();
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

    function vendorproducteditrequest()
    {
        return $products = Product::query()
            ->where('user_id', auth()->id())
            ->whereHas('pendingproduct')
            ->when(request('search'), fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }


    function vendorproducteditrequestview(int $id)
    {
        $product =  Product::query()
            ->where('user_id', auth()->id())
            ->withwhereHas('pendingproduct')
            ->find($id);

        if (!$product) {
            return $this->response('Product not found');
        }

        return $product;
    }
}

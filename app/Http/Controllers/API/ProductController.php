<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductDetails;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function ProductIndex()
    {
        $product = Product::when(request('status') == 'pending', function ($q) {
            return $q->where('status', 'pending');
        })
            ->when(request('search'), fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))

            ->when(request('status') == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->when(request('status') == 'rejected', function ($q) {
                return $q->where('status', 'rejected');
            })
            ->with('specifications','category', 'brand', 'subcategory', 'productImage','vendor:id,name')
            ->latest()->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }
    function catecoryToSubcategory($id){
        $category = Category::where([
            'status'=>'active',
            'id'=>$id
        ])->first();


        if($category){
            return response()->json([
                'status'=>200,
                'message'=>Subcategory::where([
                    'category_id'=>$category->id,
                    'status'=>'active'
                ])->latest()->get()
            ]);
        }else{
            return response()->json([
                'status'=>400,
                'message'=>[]
            ]);
        }
    }

    function updateStatus(Request $request, $id)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'status' => 'required',
                'rejected_details' => 'nullable'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $product = Product::findOrFail($id);
            $product->status = $request->status;
            $product->rejected_details = $request ?->rejected_details ?? '';
            $product->save();

            return response()->json([
                'status' => 200,
                'messaage' => 'Updated successfully!'
            ]);
        }
    }

    public function VendorProductStore(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|unique:products|max:255',
            //  'slug'=>'required',
            'status' => 'required',
            'category_id' => 'required',
            'qty' => 'required',
            'selling_price' => 'required',
            'original_price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $product = new Product;
            $product->category_id = $request->input('category_id');
            $product->subcategory_id = $request->input('subcategory_id');
            $product->brand_id = $request->input('brand_id');
            $product->user_id = Auth::user()->id;
            $product->name = $request->input('name');
            $product->slug = Str::slug($request->name);
            $product->short_description = $request->input('short_description');
            $product->long_description = $request->input('long_description');
            $product->selling_price = $request->input('selling_price');
            $product->original_price = $request->input('original_price');
            $product->qty = $request->input('qty');
            $product->status = $request->input('status');
            $product->meta_title = $request->input('meta_title');
            $product->meta_keyword = $request->input('meta_keyword');
            $product->meta_description = $request->input('meta_description');
            // $product->specification      = json_encode($request->specification);
            // $product->specification_ans  = json_encode($request->specification_ans);
            $product->request = 0;
            $product->tags  = json_encode($request->tags);
            $product->discount_type = $request->input('discount_type');
            $product->discount_rate = $request->input('discount_rate');


            $product->save();

            $product->colors()->attach($request->colors);
            $product->sizes()->attach($request->sizes);

            $productId = $product->id;
            $images = $request->file('images');
            foreach ($images as $image) {
                // image01 upload
                $name =  time() . '-' . $image->getClientOriginalName();
                $uploadpath = 'uploads/product/';
                $image->move($uploadpath, $name);
                $imageUrl = $uploadpath . $name;

                $proimage = new ProductImage();
                $proimage->product_id = $productId;
                $proimage->image = $imageUrl;
                $proimage->save();
            }


            return response()->json([
                'status' => 200,
                'message' => 'Product Added Sucessfully',
            ]);
        }
    }



    public function ProductEdit($id)
    {
        $product = Product::find($id);
        if ($product) {
            // 'colors','sizes',
            return response()->json([
                'status' => 200,
                'product' => $product->load('specifications','category','subcategory','brand','productImage','productdetails','vendor')
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Product Id Found'
            ]);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        // $image_path = app_path("uploads/product/{$product->image}");

        // if (File::exists($image_path)) {
        //     unlink($image_path);
        // }
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

    public function UpdateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products|max:255',
            'status' => 'required',
            'category_id' => 'required',
            'image' => 'required',
            'qty' => 'required',
            'selling_price' => 'required',
            'original_price' => 'required',

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
                $product->user_id = $request->input('user_id');
                // $product->user_id=Auth::user()->id;
                $product->slug =  slugUpdate(Product::class,$request->name,$id);
                $product->name = $request->input('name');
                $product->short_description = $request->input('short_description');
                $product->long_description = $request->input('long_description');
                $product->selling_price = $request->input('selling_price');
                $product->original_price = $request->input('original_price');
                $product->qty = $request->input('qty');
                // $product->status = $request->input('status');
                $product->meta_title = $request->input('meta_title');
                $product->meta_keyword = $request->input('meta_keyword');
                $product->meta_description = $request->input('meta_description');
                // $product->product_color = $request->input('product_color');
                // $product->product_size = $request->input('product_size');
                $product->tags = $request->input('tags');
                $product->commision_type = $request->input('commision_type');

                $product->colors()->attach($request->colors);
                $product->sizes()->attach($request->sizes);


                if ($request->hasFile('image')) {
                    $path = $product->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/product/', $filename);
                    $product->image = 'uploads/product/' . $filename;
                }

                $productId = $product->id;
                $update_images = ProductImage::where('product_id', $productId)->get();
                $images = $request->file('image');
                if ($images) {
                    foreach ($images as $image) {
                        // image01 upload
                        $name =  time() . '-' . $image->getClientOriginalName();
                        $uploadpath = 'public/backend/product/';
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

    public function AllCategory()
    {
        $category = Category::when(request('search'),fn($q, $name)=>$q->where('name','like',"%{$name}%"))
        ->latest()->paginate(15);
        return response()->json([
            'status' => 200,
            'category' => $category,
        ]);
    }

    public function AllBrand()
    {
        $brand = Brand::all();
        return response()->json([
            'status' => 200,
            'brand' => $brand,
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
        if ($product->status == 'pending') {
            $product->status = 'active';
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
}

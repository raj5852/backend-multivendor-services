<?php

namespace App\Http\Controllers\API\Vendor;

use App\Enums\Status;
use App\Models\VendorSubCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorSubCategoryRequest;
use App\Http\Requests\UpdateVendorSubCategoryRequest;

class VendorSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $data =  VendorSubCategory::latest()->paginate();
       return response()->json([
        'status' => 200,
        'data' =>  $data,
       ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVendorSubCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVendorSubCategoryRequest $request)

    {
       $validateData = $request->validated();
       $slug = slugCreate(VendorSubCategory::class, $validateData['name']);

       VendorSubCategory::create([
            'vendor_category_id'  => $validateData['vendor_category_id'],
            'name'                => $validateData['name'],
            'slug'                => $slug,
            'status'              => Status::Active->value,
       ]);
       return response()->json([
            'status' => 200,
            'message' => 'Vendor Sub Category Added Suucessfully',
       ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VendorSubCategory  $vendorSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show(VendorSubCategory $vendorSubCategory)
    {
        $data = $vendorSubCategory->with('VendorCategory')->get();
        return response()->json([
            'status'  => 200,
            'data'  => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VendorSubCategory  $vendorSubCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorSubCategory $vendorSubCategory)
    {
        return response()->json([
            'status'  => 200,
            'data'  => $vendorSubCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVendorSubCategoryRequest  $request
     * @param  \App\Models\VendorSubCategory  $vendorSubCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVendorSubCategoryRequest $request, VendorSubCategory $vendorSubCategory)
    {
        $validateData = $request->validated();
        $vendorSubCategory->vendor_category_id = $validateData['vendor_category_id'];
        $vendorSubCategory->name = $validateData['name'];
        $vendorSubCategory->status = $validateData['status'];
        $vendorSubCategory->save();
        return response()->json([
            'status' => 200,
            'message' => 'Vendor Sub Category Updated Successfully',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VendorSubCategory  $vendorSubCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorSubCategory $vendorSubCategory)
    {
        $vendorSubCategory->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Item Deleted Succussfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers\API\Vendor;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\VendorCategoryRequest;
use App\Models\VendorCategory;
use Illuminate\Http\Request;

class VendorCategoryController extends Controller
{
    public function create(VendorCategoryRequest $request)
    {
       $validateData = $request->validated();
       $slug = slugCreate(VendorCategory::class, $validateData['name']);

       VendorCategory::create([
            'name'  => $validateData['name'],
            'slug'  => $slug,
            'status'  => Status::Active->value,
       ]);
        return response()->json([
            'status' => 200,
            'message' => 'Vendor Category Added Sucessfully',
        ]);
    }

    public function edit($id)
    {
        $venCat = VendorCategory::find($id);
        return response()->json([
            'status' => 200,
            'data' => $venCat,
        ]);
    }

    public function update(VendorCategoryRequest $request, $id)
    {
       $validateData = $request->validated();

       $vendorCategory = VendorCategory::find($id);
       $vendorCategory->name = $validateData['name'];
    //    $vendorCategory->slug = slugUpdate(VendorCategory::clas s,$validateData['slug'], $vendorCategory->id );

       $vendorCategory->status = $validateData['status'];
       $vendorCategory->slug = $validateData['slug'];
       $vendorCategory->save();
       return response()->json([
        'status' => 200,
        'message' => 'Info Updated Successfully',
       ]);
    }

    public function delete($id){
       $vendorCategory = VendorCategory::find($id);
       $vendorCategory->delete();
       return response()->json([
        'status' => 200,
        'message' => 'Item Deleted Successfully',
       ]);
    }

}

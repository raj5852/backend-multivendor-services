<?php

namespace App\Http\Controllers\API\Vendor;

use App\Models\ServiceSubCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceSubCategoryRequest;
use App\Http\Requests\UpdateServiceSubCategoryRequest;

class ServiceSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ServiceSubCategory::where('user_id',auth()->user()->id)->get();
        return $this->response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreServiceSubCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceSubCategoryRequest $request)
    {
        $validatedData =  $request->validated();

        ServiceSubCategory::create([
            'user_id'=>userid(),
            'service_category_id' => $validatedData['service_category_id'],
            'name'=>$validatedData['name'],
        ]);

        return $this->response('Created successfull');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceSubCategory  $serviceSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ServiceSubCategory::where(['user_id'=>userid(),'id'=>$id])->first();
        if(!$data){
            return responsejson('Not found','fail');
        }
        return $this->response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceSubCategoryRequest  $request
     * @param  \App\Models\ServiceSubCategory  $serviceSubCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceSubCategoryRequest $request, $id)
    {
        $validateData =  $request->validated();
        $subcategory = ServiceSubCategory::where(['user_id'=>userid(),'id'=>$id])->first();
        if(!$subcategory){
            return responsejson('Not found','fail');
        }
        $subcategory->update($validateData);
        return $this->response('Updated successfull');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceSubCategory  $serviceSubCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $serviceSubCategory = ServiceSubCategory::where(['user_id'=>userid(),'id'=>$id]);
        if(!$serviceSubCategory){
            return $this->response('Not found','fail');
        }
        $serviceSubCategory->delete();
        return $this->response('Deleted successfull');
    }
}
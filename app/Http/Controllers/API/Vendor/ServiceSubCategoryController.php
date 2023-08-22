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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreServiceSubCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceSubCategoryRequest $request)
    {
        return $request->validated();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceSubCategory  $serviceSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceSubCategory $serviceSubCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceSubCategoryRequest  $request
     * @param  \App\Models\ServiceSubCategory  $serviceSubCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceSubCategoryRequest $request, ServiceSubCategory $serviceSubCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceSubCategory  $serviceSubCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceSubCategory $serviceSubCategory)
    {
        //
    }
}

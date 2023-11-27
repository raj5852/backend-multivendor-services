<?php

namespace App\Http\Controllers\API\Vendor;

use App\Enums\Status;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Services\Vendor\ServiceCategory as VendorServiceCategory;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(checkpermission('service-category') != 1){
            return $this->permissionmessage();
        }

        $serviceCategory = VendorServiceCategory::index();
        return $this->response($serviceCategory);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreServiceCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceCategoryRequest $request)
    {

        $validatedData = $request->validated();
        VendorServiceCategory::create($validatedData);
        return $this->response('Service Category created successfulll');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return $this->response($serviceCategoryslug);
        $vendorServicetegory =  VendorServiceCategory::show($id);
        return $vendorServicetegory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceCategoryRequest  $request
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceCategoryRequest $request, $id)
    {
        $validatedData = $request->validated();
        return  VendorServiceCategory::update($validatedData, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return  VendorServiceCategory::delete($id);

    }
}

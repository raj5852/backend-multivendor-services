<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\VendorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorServiceRequest;
use App\Http\Requests\UpdateVendorServiceRequest;
use App\Services\Vendor\ProductService;

class VendorServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendorService =  VendorService::where(['user_id'=>userid()])->paginate(10);
        return $this->response($vendorService);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVendorServiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVendorServiceRequest $request)
    {
        ProductService::store();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VendorService  $vendorService
     * @return \Illuminate\Http\Response
     */
    public function show(VendorService $vendorService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVendorServiceRequest  $request
     * @param  \App\Models\VendorService  $vendorService
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVendorServiceRequest $request, VendorService $vendorService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VendorService  $vendorService
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorService $vendorService)
    {
        //
    }
}

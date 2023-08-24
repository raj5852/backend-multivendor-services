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
        $vendorService =  VendorService::where(['user_id' => userid()])
            ->with(['servicepackages', 'serviceimages'])
            ->paginate(10);
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
        $data =  $request->validated();
        ProductService::store($data);
        return $this->response('Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VendorService  $vendorService
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendorService = VendorService::where(['user_id' => userid(), 'id' => $id])
            ->with(['servicepackages', 'serviceimages'])
            ->first();

        if (!$vendorService) {
            return responsejson('Not found', 'fail');
        }

        return $this->response($vendorService);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVendorServiceRequest  $request
     * @param  \App\Models\VendorService  $vendorService
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVendorServiceRequest $request,$id)
    {
        $data = $request->validated();
        ProductService::update($data,$id);
        return $this->response('Updated successfull!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VendorService  $vendorService
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data =  VendorService::where(['user_id'=>userid(),'id'=>$id])->first();
        if(!$data){
            return responsejson('Not found','fail');
        }
        $data->delete();

        return $this->response('Deleted successfull!');
    }
}

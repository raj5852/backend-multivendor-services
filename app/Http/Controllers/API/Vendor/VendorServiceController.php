<?php

namespace App\Http\Controllers\API\Vendor;

use App\Models\VendorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceDeliveryRequest;
use App\Http\Requests\StoreVendorServiceRequest;
use App\Http\Requests\UpdateVendorServiceRequest;
use App\Http\Requests\VendorOrderStatusRequest;
use App\Models\Category;
use App\Models\ServiceCategory;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\Vendor\ProductService;
use Carbon\Carbon;

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
        // $user = User::find(userid())->usersubscription;
        // return $user;
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
    public function update(UpdateVendorServiceRequest $request, $id)
    {
        $data = $request->validated();
        ProductService::update($data, $id);
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
        $data =  VendorService::where(['user_id' => userid(), 'id' => $id])->first();
        if (!$data) {
            return responsejson('Not found', 'fail');
        }
        $data->delete();

        return $this->response('Deleted successfull!');
    }

    function serviceorders()
    {
        $order = ServiceOrder::where('vendor_id', userid())
            ->with(['customerdetails', 'servicedetails', 'packagedetails'])
            ->latest()
            ->paginate(10);

        return $this->response($order);
    }

    function statusChange(VendorOrderStatusRequest $request)
    {
        $validateData = $request->validated();
        $serviceOrder = ServiceOrder::find($validateData['service_order_id']);
        $serviceOrder->status = $validateData['status'];
        $time = $serviceOrder->packagedetails->time;

        $timer = Carbon::now()->addDay($time);

        $serviceOrder->timer = $timer;
        $serviceOrder->save();

        return $this->response('Updated successfull');
    }

    function deliverytocustomer(ServiceDeliveryRequest $request)
    {
        // $validateData = $request->validated();
        // $validateData
    }

    function ordersview($id)
    {
        $data =  ServiceOrder::where(['vendor_id' => userid(), 'id' => $id,'is_paid'=>1])->first();
        if (!$data) {
            return responsejson('Not found', 'fail');
        }

        $order = ServiceOrder::where(['vendor_id'=> userid(),'is_paid'=>1])
            ->with(['customerdetails', 'servicedetails', 'packagedetails', 'files', 'orderdelivery' => function ($query) {
                $query->with('deliveryfiles');
            }])
            ->find($id);

        return $this->response($order);
    }

    function categorysubcategory(){
        $data = ServiceCategory::query()->with('servicesubCategories')->get();
        return $this->response($data);
    }
}

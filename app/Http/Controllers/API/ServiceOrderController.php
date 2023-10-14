<?php

namespace App\Http\Controllers\API;

use App\Models\ServiceOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerServiceStatus;
use App\Http\Requests\StoreServiceOrderRequest;
use App\Http\Requests\UpdateServiceOrderRequest;
use App\Models\ServicePackage;
use App\Services\CustomerService;
use App\Services\ServiceService;
use App\Services\SosService;

class ServiceOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $serviceOrder = ServiceOrder::query()
            ->where(['user_id' => userid(), 'is_paid' => 1])
            ->with(['servicedetails', 'packagedetails', 'vendor'])
            ->when(request('status'),function($request){
                $request->where('status',request('status'));
            })
            ->latest()
            ->paginate(10);

        return $this->response($serviceOrder);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreServiceOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceOrderRequest $request)
    {
        $validateData = $request->validated();
        return    ServiceService::store($validateData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ServiceOrder::where(['user_id' => userid(), 'id' => $id])->first();
        if (!$data) {
            return responsejson('Not found', 'fail');
        }

        $serviceOrder = ServiceOrder::where('user_id', userid())->with(['servicedetails', 'packagedetails', 'requirementsfiles', 'vendor:id,name,email', 'orderdelivery' => function ($query) {
            $query->with('deliveryfiles');
        }])->find($id);
        return $this->response($serviceOrder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateServiceOrderRequest  $request
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceOrderRequest $request, ServiceOrder $serviceOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceOrder $serviceOrder)
    {
        //
    }
    function status(CustomerServiceStatus $request)
    {
        $validateData = $request->validated();
        return  CustomerService::service($validateData);
    }
}

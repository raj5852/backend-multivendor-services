<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerServiceShow;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class ServiceOrderShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $serviceOrderDetails  = ServiceOrder::with(['customerdetails', 'vendor', 'servicedetails'])->latest()->paginate(10);
        return $this->response($serviceOrderDetails);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ServiceOrder::with(['customerdetails', 'servicedetails', 'packagedetails', 'requirementsfiles', 'vendor', 'orderdelivery' => function ($query) {
            $query->with('deliveryfiles');
        }])->find($id);
        return $this->response($data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerServiceShow $request,$id)
    {
        $validatedData = $request->validated();
        $serviceOrder = ServiceOrder::find($id);
        $serviceOrder->status = $validatedData['status'];
        $serviceOrder->save();

        return $this->response('Updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

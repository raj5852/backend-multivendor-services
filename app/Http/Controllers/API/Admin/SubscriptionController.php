<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Services\Admin\SubscriptionService;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $data =  Subscription::all();
       return response()->json([
            'status' => 200,
            'data' => $data,
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
     * @param  \App\Http\Requests\StoreSubscriptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Subscription::find($id);
        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubscriptionRequest  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSubscriptionRequest $request, $id)
    {

        $validateData = $request->validated();
        SubscriptionService::update($validateData, $id);
        return $this->response('Subsciption Updated Successfuly');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        //
    }

    function requirement(SubscriptionRequest $request){
        $validateData = $request->validated();

        $subscription = Subscription::find($validateData['subscription_id']);
        $subscription->service_qty = request('service_qty');
        $subscription->product_qty = request('product_qty');
        $subscription->affiliate_request = request('affiliate_request');

         $subscription->service_qty = $validateData['product_request'];
        $subscription->product_qty = $validateData['product_approve'];
        $subscription->affiliate_request = $validateData['service_create'];
        $subscription->save();

        return $this->response('Successfull');
    }
}

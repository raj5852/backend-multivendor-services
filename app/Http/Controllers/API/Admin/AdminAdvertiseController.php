<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\AdminAdvertise;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminAdvertiseRequest;
use App\Http\Requests\UpdateAdminAdvertiseRequest;
use App\Services\Admin\AdminAdvertiseService;

class AdminAdvertiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AdminAdvertise::with('AdvertiseAudienceFile', 'advertisePlacement', 'advertiseLocationFiles')->orderBy('id', 'DESC')->get();
        return $this->response($data);
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
     * @param  \App\Http\Requests\StoreAdminAdvertiseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminAdvertiseRequest $request )
    {
        $validatData = $request->validated();
        AdminAdvertiseService::create($validatData);
        return $this->response('Advertise Added Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdminAdvertise  $adminAdvertise
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $userID = userid();

        $userID = userid();
        $adminAdvertise = AdminAdvertise::with('AdvertiseAudienceFile', 'advertisePlacement', 'advertiseLocationFiles')->find($id);
        if ($adminAdvertise) {
            return response()->json([
                'status' => 200,
                'product' => $adminAdvertise,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No adminAdvertise data Found'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdminAdvertise  $adminAdvertise
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userID = userid();
        $adminAdvertise = AdminAdvertise::with('AdvertiseAudienceFile', 'advertisePlacement', 'advertiseLocationFiles')->find($id);
        return response()->json([
            'status' => 200,
            'product' => $adminAdvertise,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdminAdvertiseRequest  $request
     * @param  \App\Models\AdminAdvertise  $adminAdvertise
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminAdvertiseRequest $request, AdminAdvertise $adminAdvertise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdminAdvertise  $adminAdvertise
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdminAdvertise $adminAdvertise)
    {
        //
    }
}

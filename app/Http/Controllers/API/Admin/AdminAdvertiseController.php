<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\AdminAdvertise;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAdminStatusProgress;
use App\Http\Requests\AdvertiseDeliveryRequest;
use App\Http\Requests\CancelAdminAdvertiseRequest;
use App\Http\Requests\StoreAdminAdvertiseRequest;
use App\Http\Requests\UpdateAdminAdvertiseRequest;
use App\Models\User;
use App\Services\Admin\AdminAdvertiseService;
use Illuminate\Support\Facades\DB;

class AdminAdvertiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AdminAdvertise::query()
            ->latest()
            ->when(request('search'), fn ($q, $search) => $q->where('unique_id', 'like', "%{$search}%"))
            ->where('is_paid',1)
            ->select('id','campaign_name','campaign_objective','budget_amount','start_date','end_date','is_paid','created_at','status','unique_id')
            ->paginate(10);

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
    public function store(StoreAdminAdvertiseRequest $request)
    {

        $advertise =  AdminAdvertiseService::create($request->validated());

        return $this->response($advertise);
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
        $adminAdvertise = AdminAdvertise::with('AdvertiseAudienceFile', 'advertisePlacement', 'advertiseLocationFiles','files')->find($id);
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdminAdvertiseRequest  $request
     * @param  \App\Models\AdminAdvertise  $adminAdvertise
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminAdvertiseRequest $request,  $id)
    {
        $validatData = $request->validated();
        AdminAdvertiseService::update($validatData, $id);
        return $this->response('Advertise Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdminAdvertise  $adminAdvertise
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = AdminAdvertise::find($id);
        if ($data) {
            $data->delete();
            return $this->response('Item Deleted Successfully !');
        } else {
            return $this->response('Item Not found!');
        }
    }

    function status(AdminAdminStatusProgress $request)
    {
        $validateData = $request->validated();
        $advertise = AdminAdvertise::find($validateData['advertise_id']);
        $advertise->status = "progress";
        $advertise->save();

        return $this->response('Progress successfully!');
    }

    function delivery(AdvertiseDeliveryRequest $request)
    {

        DB::transaction(function () {
            $advertise = AdminAdvertise::find(request('advertise_id'));
            $advertise->status = "delivered";
            $advertise->save();

            if (request()->hasFile('files')) {
                foreach (request('files') as $file) {
                    $filename = uploadany_file($file);

                    $advertise->files()->create([
                        'name' => $filename
                    ]);
                }
            }
        });

        return $this->response('Delivered successfully!');
    }

    function cancel(CancelAdminAdvertiseRequest $request)
    {

        $advertise = AdminAdvertise::find(request('advertise_id'));
        $advertise->status = "cancel";
        $advertise->reason = request('reason');
        $advertise->save();

        return $this->response('Cancel successfully!');
    }
}

<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\SupportBox;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportBoxRequest;
use App\Http\Requests\UpdateSupportBoxRequest;
use App\Services\SosService;
use Illuminate\Support\Facades\File;

class SupportBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if(checkpermission('support') != 1){
        //     return $this->permissionmessage();
        // }

        $supportData = SupportBox::query()
            ->with(['user', 'latestTicketreplay','category:id,name','problem_topic:id,name'])
            ->withCount(['ticketreplay as total_admin_replay'=>function($query){

                $query->whereHas('user',function($query){
                    $query->where('role_as',1);
                });
            }])
            ->when(checkpermission('support') != 1,function($query){
                $query->whereHas('supportassigned',function($query){
                    $query->where('user_id',auth()->id());
                });
            })
            ->latest()
            ->paginate(10);

        return $this->response($supportData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSupportBoxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupportBoxRequest $request)
    {
        // $data = $request->all();
        // SosService::ticketcreate($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupportBox  $supportBox
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // if(checkpermission('support') != 1){
        //     return $this->permissionmessage();
        // }

        $supportBox = SupportBox::query()
        ->when(checkpermission('support') != 1,function($query){
            $query->whereHas('supportassigned',function($query){
                $query->where('user_id',auth()->id());
            });
        })
        ->find($id);
        if (!$supportBox) {
            return responsejson('Not found', 'fail');
        }

        $data =  $supportBox->load(['ticketreplay' => function ($query) {
            $query->with(['user', 'file']);
        }]);

        return $this->response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSupportBoxRequest  $request
     * @param  \App\Models\SupportBox  $supportBox
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupportBoxRequest $request, SupportBox $supportBox)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupportBox  $supportBox
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $support = SupportBox::query()
        ->when(checkpermission('support') != 1,function($query){
            $query->whereHas('supportassigned',function($query){
                $query->where('user_id',auth()->id());
            });
        })
        ->find($id);
        if (File::exists($support->file)) {
            File::delete($support->file);
        }
        $support->delete();

        return $this->response('Deleted successfull');
    }
}

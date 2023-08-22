<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\SupportProblemTopic;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportProblemTopicRequest;
use App\Http\Requests\UpdateSupportProblemTopicRequest;

class SupportProblemTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('support_problem_topics')->where('deleted_at',null)->get();
        return responsejson($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSupportProblemTopicRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupportProblemTopicRequest $request)
    {
        $validatedData = $request->validated();
        SupportProblemTopic::create($validatedData);
        return $this->response('Created Successfull');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupportProblemTopic  $supportProblemTopic
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supportProblemTopic = SupportProblemTopic::find($id);
        if(!$supportProblemTopic){
            return responsejson('Not found','fail');
        }
        return $this->response($supportProblemTopic);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSupportProblemTopicRequest  $request
     * @param  \App\Models\SupportProblemTopic  $supportProblemTopic
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupportProblemTopicRequest $request, SupportProblemTopic $supportProblemTopic)
    {
        $validatedData =  $request->validated();
        SupportProblemTopic::create($validatedData);

        return $this->response('Updated successfull');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupportProblemTopic  $supportProblemTopic
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = SupportProblemTopic::find($id);
        if(!$data){
            return responsejson('Not found','fail');
        }
        $data->delete();
        return $this->response('Deleted successfull');
    }
}

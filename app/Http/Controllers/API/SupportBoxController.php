<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportBoxRequest;
use App\Http\Requests\TIcketReviewRequest;
use App\Models\SupportBox;
use App\Models\TicketReply;
use App\Services\SosService;
use Illuminate\Http\Request;
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
        $data = SupportBox::where('user_id',auth()->user()->id)->latest()->paginate(10);
        return $this->response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupportBoxRequest $request)
    {
        $data = $request->all();
        SosService::ticketcreate($data);
        return $this->response('Created successfull');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supportBox = SupportBox::where(['id'=>$id,'user_id'=>auth()->user()->id])->first();
        if (!$supportBox) {
            return responsejson('Not found','fail');
        }

        $data =  $supportBox->load('ticketreplay');

        return $this->response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $support = SupportBox::where(['id'=>$id,'user_id'=>auth()->user()->id])->first();
        if(File::exists($support->file)){
            File::delete($support->file);
        }
        $support->delete();

        return $this->response('Deleted successfull');
    }

    function review(TIcketReviewRequest $request){
        $data =  $request->validated();
        $ticketReply =  TicketReply::where(['id'=>$data['ticket_replie_id']])->first();

        if(!$ticketReply){
            return responsejson('Not fond','fail');
        }
        $ticketReply->rating = $data['rating'];
        $ticketReply->save();

        return $this->response('Rating successfull');
    }
}

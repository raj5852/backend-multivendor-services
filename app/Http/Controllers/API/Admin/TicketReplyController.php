<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\TicketReply;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\UpdateTicketReplyRequest;
use App\Models\SupportBox;

class TicketReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTicketReplyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTicketReplyRequest $request)
    {
        $validateData =  $request->validated();
        $validateData['user_id'] = userid();


        $ticketreplay = TicketReply::create($validateData);

        if(request()->hasFile('file')){
            $filename = uploadany_file(request('file'));
            $ticketreplay->file()->create([
                'name'=>$filename
            ]);
        }


        return $this->response('Successfull');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function show(TicketReply $ticketReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTicketReplyRequest  $request
     * @param  \App\Models\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTicketReplyRequest $request, TicketReply $ticketReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TicketReply  $ticketReply
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketReply $ticketReply)
    {
        //
    }

    function closesupportbox($id){
        $supportbox = SupportBox::find($id);
        $supportbox->is_close = 1;
        $supportbox->save();

        return $this->response('Ticket colse successfull!');
    }

    function progress($id){
        $supportbox = SupportBox::find($id);
        $supportbox->status = 'progress';
        $supportbox->save();

        return "Status updated successfull!";
    }
}

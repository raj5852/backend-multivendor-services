<?php

namespace App\Http\Controllers;

use App\Models\AdminAdvertise;
use Illuminate\Http\Request;

class AdvertiseController extends Controller
{
    function index(){

        $data = AdminAdvertise::where('user_id',userid())->where('is_paid',1) ->latest()->paginate(10);
        return $this->response($data);
    }

    function show($id){
        $data =  AdminAdvertise::where(['user_id'=>userid()])
        ->where('is_paid',1)
        ->with('AdvertiseAudienceFile', 'advertisePlacement', 'advertiseLocationFiles','files')->find($id);

        return $this->response($data);
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\AdminAdvertise;
use Illuminate\Http\Request;

class AdvertiseController extends Controller
{
    function index(){

        $data = AdminAdvertise::where('user_id',userid())->latest()->paginate(10);
        return $this->response($data);
    }

    function show($id){
        $data =  AdminAdvertise::where(['user_id'=>userid()])
        ->with('AdvertiseAudienceFile', 'advertisePlacement', 'advertiseLocationFiles','files')->find($id);

        return $this->response($data);
    }


}

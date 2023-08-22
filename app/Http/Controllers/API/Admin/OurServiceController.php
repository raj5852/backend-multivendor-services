<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OurServiceController extends Controller
{
    public function index(){
        $services = OurService::latest()->paginate(6);
        return response()->json([
            'status' => 200,
            'data' => $services,
        ]);
    }

    public function storeOurService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'icon'        => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            OurService::create($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Data Inserted Successfully !',
            ]);
        }
    }

    public function showOurService($id){
        $service = OurService::find($id);
        if($service){
            return response()->json([
                'status' => 200,
                'datas' =>$service,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Service Data Found',
            ]);
        }
    }

    public function editOurService($id){
        $service = OurService::find($id);
        if($service){
            return response()->json([
                'status' => 200,
                'datas' =>$service,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Service Two Found',
            ]);
        }
    }


    public function updateOurService(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'icon'        => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            OurService::find($id)->update($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Service Updated Successfully !',
            ]);
        }
    }

    public function deleteOurService($id){
        OurService::find($id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Service Deleted Successfully !',
        ]);
    }


}

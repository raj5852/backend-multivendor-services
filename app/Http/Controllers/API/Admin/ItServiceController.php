<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Itservice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItServiceController extends Controller
{

    public function index(){
        $data = Itservice::latest()->paginate(8);
        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }


    public function storeItService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon'       => 'required',
            'title'       => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $data = $request->all();
            Itservice::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Data services Successfully !',
            ]);
        }
    }


    public function showtService($id){
        $slider = Itservice::find($id);

        if($slider){
            return response()->json([
                'status' => 200,
                'datas' => $slider,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No services Found',
            ]);
        }
    }


    public function updateItService(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'icon'       => 'required',
            'title'       => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $data = $request->all();
            Itservice::find($id)->update($data);
            return response()->json([
                'status' => 200,
                'message' => 'It services Updated Successfully !',
            ]);
        }
    }

    public function deleteItService($id){
        $data = Itservice::find($id);
        $data->delete();
        return response()->json([
            'status' => 200,
            'message' => 'services Deleted Successfully !',
        ]);
    }

}

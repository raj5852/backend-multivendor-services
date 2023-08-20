<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Service\Vendor\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeSliderController extends Controller
{
    public function index(){
        return response()->json([
            'status' => 200,
            'data' => ProductService::showSLider(),
        ]);
    }

    public function storeSlider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'thumbal'     => 'required|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $data = $request->all();
            if($request->thumbal){
                $data['thumbal'] = fileUpload($request->thumbal, 'uploads/sliders/', 9020, 405);
            }
            Slider::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Data Inserted Successfully !',
            ]);
        }
    }


    public function editSlider($id){
        $slider = Slider::find($id);

        if($slider){
            return response()->json([
                'status' => 200,
                'datas' => $slider,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Slider Found',
            ]);
        }
    }


    public function updateSlider(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'thumbal'     => 'mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $old_image = Slider::find($id);
            $data = $request->all();
            if ($request->thumbal) {
                if ($old_image->thumbal) {
                    unlink($old_image->thumbal);
                }
                $data['thumbal'] = fileUpload($request->thumbal, 'uploads/sliders/', 9020, 405);
            }
            Slider::find($id)->update($data);
            return response()->json([
                'status' => 200,
                'message' => 'Slider Updated Successfully !',
            ]);
        }
    }

    public function deleteSlider($id){
        $data = Slider::find($id);
            if ($data->thumbal) {
                unlink($data->thumbal);
            }
        $data->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Slider Deleted Successfully !',
        ]);
    }





}

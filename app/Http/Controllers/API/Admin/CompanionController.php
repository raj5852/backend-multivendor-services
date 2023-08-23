<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Companion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanionController extends Controller
{
    public function index(){
        $companion = Companion::latest()->get();
        return response()->json([
            'status' => 200,
            'data' => $companion,
        ]);
    }

    public function storeCompanion(Request $request)
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
            Companion::create($request->all());
            return response()->json([
                'status'  => 200,
                'message' => 'Companion Created Successfully !',
            ]);
        }
    }


    public function showCompanion($id)
    {
        $companion = Companion::find($id);
        if($companion){
            return response()->json([
                'status' => 200,
                'datas'  =>$companion,
            ]);
        }else{
            return response()->json([
                'status'  => 404,
                'message' => 'No Companion Found',
            ]);
        }
    }


    public function updateCompanion(Request $request, $id)
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
            Companion::find($id)->update($request->all());
            return response()->json([
                'status'  => 200,
                'message' => 'Companion Updated Successfully !',
            ]);
        }
    }

    public function deleteCompanion($id){
        Companion::find($id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Companion Deleted Successfully !',
        ]);
    }


}

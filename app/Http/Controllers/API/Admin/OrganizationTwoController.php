<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationTwoController extends Controller
{
    public function index(){
        $orgtwo = OrganizationTwo::all();
        return response()->json([
            'status' => 200,
            'data' => $orgtwo,
        ]);
    }

    public function storeOrganizationTwo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'icon'        => 'required|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $data = $request->all();
            if($request->icon){
                $data['icon'] = fileUpload($request->icon, 'uploads/organizationtwo/', 30, 33);
            }
            OrganizationTwo::create($data);
            return response()->json([
                'status' => 200,
                'message' => 'Data Inserted Successfully !',
            ]);
        }
    }


    public function editOrganizationTwo($id){
        $OrgTwo = OrganizationTwo::find($id);

        if($OrgTwo){
            return response()->json([
                'status' => 200,
                'datas' => $OrgTwo,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Organization Two Found',
            ]);
        }
    }


    public function updateOrganizationTwo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'icon'        => 'mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $old_image = OrganizationTwo::find($id);
            $data = $request->all();
            if ($request->icon) {
                if ($old_image->icon) {
                    unlink($old_image->icon);
                }
                $data['icon'] = fileUpload($request->icon, 'uploads/organizationtwo/', 30, 33);
            }
            OrganizationTwo::find($id)->update($data);
            return response()->json([
                'status' => 200,
                'message' => 'Organization Two Updated Successfully !',
            ]);
        }
    }

    public function deleteOrganizationTwo($id){
        $data = OrganizationTwo::find($id);
            if ($data->icon) {
                unlink($data->icon);
            }
        $data->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Organization Two Deleted Successfully !',
        ]);
    }


}

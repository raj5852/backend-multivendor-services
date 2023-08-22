<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationTwoController extends Controller
{
    public function index(){
        $orgtwo = OrganizationTwo::latest()->paginate(3);
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
            'icon'        => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            OrganizationTwo::create($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Data Inserted Successfully !',
            ]);
        }
    }

    public function showOrganizationTwo($id)
    {
        $OrgTwo = OrganizationTwo::find($id);
        if($OrgTwo){
            return response()->json([
                'status' => 200,
                'datas' => $OrgTwo,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Organization Two Infos Found',
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
                'message' => 'No Organization Two Infos Found',
            ]);
        }
    }


    public function updateOrganizationTwo(Request $request, $id)
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
            OrganizationTwo::find($id)->update($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Organization Two Updated Successfully !',
            ]);
        }
    }

    public function deleteOrganizationTwo($id){
        OrganizationTwo::find($id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Organization Two Deleted Successfully !',
        ]);
    }


}

<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Service\Vendor\Frontend;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function index(){
        $organizations = Organization::latest()->get();
        return response()->json([
            'status' => 200,
            'data' =>  $organizations,
        ]);
    }

    public function storeOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $creaedData = Organization::create($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Data Inserted Successfully !',
            ]);
        }
    }

    public function showOrganization($id)
    {
        $organization = Organization::find($id);
        if($organization){
            return response()->json([
                'status' => 200,
                'datas' => $organization,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Found ! ',
            ]);
        }
    }


    public function editOrganization($id){
        $organization = Organization::find($id);
        if($organization){
            return response()->json([
                'status' => 200,
                'datas' => $organization,
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Found ! ',
            ]);
        }
    }


    public function updateOrganization(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description'   => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            Organization::find($id)->update($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Organization Updated Successfully !',
            ]);
        }
    }

    public function deleteOrganization($id){
        Organization::find($id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Organization Deleted Successfully !',
        ]);
    }


}

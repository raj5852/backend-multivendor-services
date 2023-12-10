<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddManagerRequest;
use App\Http\Requests\RolePermissionRequest;
use App\Http\Requests\RolePermissionUpdateRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{


    function rolepermission(RolePermissionRequest $request){
        $role = Role::create(['name' => request('role')]);

        $role->syncPermissions(request('permission'));
        return response()->json(['success'=>true,'message'=>'Role created successfully']);
    }

    function allroll(){
        return response()->json(Role::all());
    }

    function allpermission(){
        return response()->json(Permission::all());
    }

    function rolewithpermission($id){
        $role = Role::find($id);
        if(!$role){
            return response()->json('Not found');
        }

        return response()->json($role->load('permissions'));
    }

    function rolepermissionupdate(RolePermissionUpdateRequest $request,$id){

        $role =  Role::find($id);

        if(!$role){
            return response()->json('Not found');
        }

        $role->name = request('role');
        $role->save();

        $role->syncPermissions(request('permission'));

        return response()->json(['success'=>true,'message'=>'Updated successfully']);
    }

    function addmanager(AddManagerRequest $request){
        $user = new User();
        $user->name = request('name');
        $user->email = request('email');
        $user->password = bcrypt(request('password'));
        $user->role_as = 1;
        $user->number =  request('number');
        $user->status = 'active';
        $user->balance = 0;
        $user->save();

        DB::table('model_has_roles')->insert([
            'role_id'=>request('role'),
            'model_type'=>'App\Models\User',
            'model_id'=>$user->id
        ]);

        return response()->json(['success'=>true,'message'=>'created successfully']);
    }

    function getaddmanager($id){

        $user = User::find(auth()->id());
        if(!$user){
            return response()->json('Not found');
        }
        return response()->json($user->load('roles'));

    }


    function updateamanager(UpdateManagerRequest $request, $id){

        $user = User::find($id);


        DB::table('model_has_roles')->where('model_id',$id)->delete();

        // $user->assignRole(request('role'));
        DB::table('model_has_roles')->insert([
            'role_id'=>request('role'),
            'model_type'=>'App\Models\User',
            'model_id'=>$user->id
        ]);

        return response()->json(['success'=>true,'message'=>'Updated successfully']);

    }

    function allmanagerlist(){
        $data = User::where('role_as',1)->where('id','!=',1)
            ->with('roles')
        ->get();
        return $data;
    }

}
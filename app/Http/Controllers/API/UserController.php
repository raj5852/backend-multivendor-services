<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;

class UserController extends Controller
{


    function updateStatus(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }else{
            $user = User::find($id);
            $user->status = $request->status;
            $user->save();

            return response()->json([
                'status'=>200,
                'message'=>'Updated successfully!'
            ]);

        }
    }
    public function VendorView(Request $request)
    {


        $vendor = User::where('role_as', '2')
            ->when(request('name') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when(request('name') == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->when($request->email,fn($q, $email)=>$q->where('email','like',"%{$email}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'vendor' => $vendor,
        ]);
    }

    public function VendorStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|max:255',
            'name' => 'required',
            'number' => 'required',
            'status' => 'required',
            'password' => 'required:min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            if($request->amount >= 0){
                return "Amount Wrong";
            }
            $vendor = new User();
            $vendor->name = $request->input('name');
            $vendor->email = $request->input('email');
            $vendor->password = Hash::make($request['password']);
            $vendor->status = $request->input('status');
            $vendor->number = $request->input('number');
            $vendor->amount = $request->input('amount');
            $vendor->role_as = '2';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('uploads/vendor/', $filename);
                $vendor->image = 'uploads/vendor/' . $filename;
            }

            $vendor->save();
            return response()->json([
                'status' => 200,
                'message' => 'Vendor Added Sucessfully',
            ]);
        }
    }



    public function VendorEdit($id)
    {
        $vendor = User::find($id);
        if ($vendor) {
            return response()->json([
                'status' => 200,
                'vendor' => $vendor
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Vendor Id Found'
            ]);
        }
    }

    public function UpdateVendor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'status' => 'required|max:191',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $vendor = User::find($id);
            if ($vendor) {

                $vendor->name = $request->input('name');
                $vendor->email = $request->input('email');
                $vendor->status = $request->input('status');
                $vendor->number = $request->input('number');
                $vendor->image = $request->input('image');



                if ($request->hasFile('image')) {
                    $path = $vendor->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/vendor/', $filename);
                    $vendor->image = 'uploads/vendor/' . $filename;
                }


                $vendor->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Vendor Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Vendor Not Found',
                ]);
            }
        }
    }


    public function VendorDelete($id)
    {
        $vendor = User::find($id);
        // $image_path = app_path("uploads/vendor/{$vendor->image}");

        // if (File::exists($image_path)) {
        //     unlink($image_path);
        // }

        // if ($vendor->image) {
        //   unlink('uploads/vendor'.$vendor->image);
        // }

        if ($vendor) {
            $vendor->delete();
            return response()->json([
                'status' => 200,
                'message' => 'vendor Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Vendor ID Found',
            ]);
        }
    }


    public function AffiliatorView(Request $request)
    {
        $affiliator = User::where('role_as', '3')
            ->when(request('name') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when(request('name') == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->when($request->email,fn($q, $email)=>$q->where('email','like',"%{$email}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'affiliator' => $affiliator,
        ]);
    }



    public function AffiliatorStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|max:255',
            'name' => 'required',
            'number' => 'required',
            'status' => 'required',
            'password' => 'required:min:6',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $affiliator = new User();
            $affiliator->name = $request->input('name');
            $affiliator->email = $request->input('email');
            $affiliator->password = Hash::make($request['password']);
            $affiliator->status = $request->input('status');
            $affiliator->number = $request->input('number');
            $affiliator->role_as = '3';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('uploads/vendor/', $filename);
                $affiliator->image = 'uploads/vendor/' . $filename;
            }

            $affiliator->save();
            return response()->json([
                'status' => 200,
                'message' => 'Affiliator Added Sucessfully',
            ]);
        }
    }

    public function AffiliatorEdit($id)
    {
        $affiliator = User::find($id);
        if ($affiliator) {
            return response()->json([
                'status' => 200,
                'affiliator' => $affiliator
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Affiliator Id Found'
            ]);
        }
    }



    public function UpdateAffiliator(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'status' => 'required|max:191',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $affiliator = User::find($id);
            if ($affiliator) {

                $affiliator->name = $request->input('name');
                $affiliator->email = $request->input('email');
                $affiliator->status = $request->input('status');
                $affiliator->number = $request->input('number');
                $affiliator->image = $request->input('image');



                if ($request->hasFile('image')) {
                    $path = $affiliator->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/affiliator/', $filename);
                    $affiliator->image = 'uploads/affiliator/' . $filename;
                }


                $affiliator->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Affiliator Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Affiliator Not Found',
                ]);
            }
        }
    }

    public function AffiliatorDelete($id)
    {
        $vendor = User::find($id);

        // if ($vendor->image) {
        //  unlink('uploads/vendor/'.$vendor->image);
        //   }

        if ($vendor) {
            $vendor->delete();
            return response()->json([
                'status' => 200,
                'message' => 'vendor Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Vendor ID Found',
            ]);
        }
    }
}

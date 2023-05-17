<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\ProductDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function AdminProfile()
    {
        $user=User::find(Auth::user()->id);
        return response()->json([
            'status'=>200,
            'user'=>$user
        ]);
    }

    public function AdminUpdateProfile(Request $request)
    {

        $validator =  Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required|integer',
            'number2'=>'nullable|integer',
            'old_password' => 'nullable',
            'new_password' => 'nullable',
        ]);

        if ($request->has('old_password') && $request->input('old_password') !== null) {
            $validator->addRules([
                'new_password' => 'required|min:8|max:32',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->messages()
            ]);
        }


        $data = User::find(Auth::user()->id);
        $data->name = $request->name;
        $data->number = $request->number;
        $data->number2 = $request->number2;
        if ($request->old_password) {
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Old Password Not Match!'
                ]);
            }else{
                $data->password = bcrypt($request->new_password);
            }
        }


        if($request->hasFile('image'))
        {
            if(File::exists($data->image)){
                File::delete($data->image);
            }
            $image =  fileUpload($request->file('image'),'uploads/admin');
            $data->image = $image;
        }

        $data->save();
        return response()->json([
        'status'=>200,
         'message'=>'Profile Update Sucessfully',
        ]);
    }

    function RequestRejected(){

        $product=ProductDetails::with(['vendor','affiliator','product'])
        ->where('status','3')
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();




            return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }
    public function AdminRequestPending()
    {

         $product=ProductDetails::with(['vendor','affiliator','product'])
        ->where('status','2')
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();




            return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }
    function AdminRequestView($id){
        $product=ProductDetails::with(['vendor','affiliator','product' => function($query) {
            $query->with('productImage','sizes','colors');
        }])->find($id);


            return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }

    public function AdminRequestActive()
    {
         $product=ProductDetails::with(['vendor','affiliator','product'])->where('status','1')

        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })

        ->latest()->paginate(10)
        ->withQueryString();

            return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);
    }


    function AdminRequestAll(){
        $product=ProductDetails::with(['vendor','affiliator','product'])
        ->whereHas('product', function ($query)  {
            $query->where('name', 'LIKE', '%'.request('search').'%');
        })

        ->latest()->paginate(10)
        ->withQueryString();

            return response()->json([
            'status'=>200,
            'product'=>$product,
        ]);

        // return ProductDetails::with('product')->get();
    }

    public function AdminRequestBalances()
    {
        $user=User::where('balance_status',0)->get();
        return response()->json($user);
    }


    public function AdminRequestBalanceActive()
    {
        $user=User::where('balance_status',1)->get();
        return response()->json($user);
    }





    function RequestUpdate(Request $request,$id){

        $data =  ProductDetails::find($id);
        if ($data) {
            $data->status = $request->status;
                $data->reason = $request->reason;
            $data->save();
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Not found',
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'updated successfully',
        ]);
    }

}

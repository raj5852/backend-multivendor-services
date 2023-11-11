<?php

namespace App\Http\Controllers\API;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;

class UserController extends Controller
{


    function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $user = User::find($id);
            $user->status = $request->status;
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Updated successfully!'
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
            ->when(
                $request->email,
                fn ($q, $email) => $q->search($email)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'vendor' => $vendor,
        ]);
    }

    function alluserlist($status)
    {

        $vendor = User::where('role_as', '!=', '1')
            ->when($status == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->when($status == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when(request('type') == 'vendor' , function ($q) {
                return $q->where('role_as', '2');
            })
            ->when(request('type') == 'affiliate' , function ($q) {
                return $q->where('role_as', '3');
            })
            ->when(request('type') == 'user' , function ($q) {
                return $q->where('role_as', '4');
            })
            ->when(request('from') != '' && request('to') != '' , function ($q) {
                return $q->whereBetween('created_at',[Carbon::parse(request('from')), Carbon::parse(request('to'))]);
            })
            ->when(
                request('email'),
                fn ($q, $email) => $q->search($email)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'all' => $vendor,
        ]);
    }

    public function VendorStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|max:255',
            'name' => 'required',
            'number' => ['required', 'integer'],
            'status' => 'required',
            'password' => 'required:min:8',
            'balance' => ['numeric', 'min:0']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {

            $vendor = new User();
            $vendor->name = $request->input('name');
            $vendor->email = $request->input('email');
            $vendor->password = Hash::make($request['password']);
            $vendor->status = $request->input('status');
            $vendor->number = $request->input('number');
            $vendor->balance = $request->balance ?? 0;
            $vendor->role_as = '2';

            if ($request->hasFile('image')) {
                $img =  fileUpload($request->file('image'), 'uploads/vendor', 125, 125);
                $vendor->image = $img;
            }
            $vendor->save();
            // $vendor->defa
            $subscription = Subscription::find(1);
            $user = $vendor;
            $amount = 0;
            $coupon = null;
            $paymentmethod = "Manually";
            SubscriptionService::store($subscription, $user, $amount, $coupon?->id, $paymentmethod);

            return response()->json([
                'status' => 200,
                'message' => 'Vendor Added Sucessfully',
            ]);
        }
    }



    public function VendorEdit($id)
    {
        $vendor = User::find($id)->load('usersubscription.subscription:id,card_heading');
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

                if ($request->balance) {
                    if ($request->balance < 0) {
                        return response()->json(['Balance Not Valid']);
                    }
                }

                $vendor->name = $request->input('name');
                $vendor->email = $request->input('email');
                $vendor->status = $request->input('status');
                $vendor->number = $request->input('number');
                // $vendor->image = $request->input('image');
                $vendor->balance = $request->input('balance');



                if ($request->hasFile('image')) {
                    $path = $vendor->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    $img =  fileUpload($request->file('image'), 'uploads/vendor', 125, 125);
                    $vendor->image = $img;
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
            ->when(
                $request->email,
                fn ($q, $email) => $q->search($email)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'affiliator' => $affiliator,
        ]);
    }

    public function user(Request $request)
    {
        $user = User::where('role_as', '4')
            ->when(request('name') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when(request('name') == 'active', function ($q) {
                return $q->where('status', 'active');
            })
            ->when($request->email != '', fn ($q, $email) => $q->search($email))
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'user' => $user,
        ]);
    }



    public function UserStore(Request $request)
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
            $affiliator->role_as = '4';

            if ($request->hasFile('image')) {

                $img =  fileUpload($request->file('image'), 'uploads/affiliator', 125, 125);

                $affiliator->image = $img;
            }

            $affiliator->save();
            return response()->json([
                'status' => 200,
                'message' => 'User Added Sucessfully',
            ]);
        }
    }

    public function AffiliatorStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|max:255',
            'name' => 'required',
            'number' => ['required', 'numeric'],
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
            $affiliator->balance = 0;
            $affiliator->role_as = '3';

            if ($request->hasFile('image')) {

                $img =  fileUpload($request->file('image'), 'uploads/affiliator', 125, 125);

                $affiliator->image = $img;
            }
            $affiliator->save();

            $subscription = Subscription::find(13);
            $user = $affiliator;
            $amount = 0;
            $coupon = null;
            $paymentmethod = "Manually";

            SubscriptionService::store($subscription, $user, $amount, $coupon?->id, $paymentmethod);
            return response()->json([
                'status' => 200,
                'message' => 'Affiliator Added Sucessfully',
            ]);
        }
    }
    public function UserEdit($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'status' => 200,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No User Id Found'
            ]);
        }
    }



    public function AffiliatorEdit($id)
    {
        $affiliator = User::find($id)->load('usersubscription.subscription:id,card_heading');

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
            'balance'=>['numeric']
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
                $affiliator->balance = $request->input('balance');




                if ($request->hasFile('image')) {
                    $path = $affiliator->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    $img =  fileUpload($request->file('image'), 'uploads/affiliator', 125, 125);

                    $affiliator->image = $img;
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


    public function Updateuser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'status' => 'required|max:191',
            'email' => 'required',
            'number' => 'required',
            'balance' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $user = User::find($id);
            if ($user) {

                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->status = $request->input('status');
                $user->number = $request->input('number');
                $user->balance = request('balance');



                if ($request->hasFile('image')) {
                    $path = $user->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    $img =  fileUpload($request->file('image'), 'uploads/affiliator', 125, 125);

                    $user->image = $img;
                }


                $user->update();

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
    public function UserDelete($id)
    {
        $vendor = User::find($id);


        if ($vendor) {
            $vendor->delete();
            return response()->json([
                'status' => 200,
                'message' => 'User Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No User ID Found',
            ]);
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

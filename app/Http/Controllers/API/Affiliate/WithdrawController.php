<?php

namespace App\Http\Controllers\API\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{

    function index()
    {
        $search = request('search');
        $withdraw = Withdraw::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->when(request('status') == 'success', function ($q) {
                return $q->where('status', 'success');
            })
            ->when(request('status') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            })
            ->when($search != '', function ($query) use ($search) {
                $query->where('uniqid', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();



        return response()->json([
            'status' => 200,
            'message' => $withdraw
        ]);
    }

    function withdraw(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:200'],
            'bank_name' => ['required'],
            'ac_or_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'message' => $validator->messages()
            ]);
        }

        if (auth()->user()->balance >= $request->amount) {

            Withdraw::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'ac_or_number' => $request->ac_or_number,
                'holder_name' => $request->holder_name,
                'branch_name' => $request->branch_name,
                'role' => auth()->user()->role_as,
                'uniqid' => uniqid()
            ]);

            $afi = Auth::user();
            $afi->balance =  ($afi->balance - $request->amount);
            $afi->save();

            return response()->json([
                'status' => 200,
                'message' => 'Withdraw successfully!'
            ]);
        } else {

            return response()->json([
                'status' => 200,
                'message' => 'Balance not available!'
            ]);
        }
    }
}

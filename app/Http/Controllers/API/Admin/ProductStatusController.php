<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductDetails;
use App\Models\User;
use Illuminate\Http\Request;

class ProductStatusController extends Controller
{

    public function AdminRequestPending()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->with(['vendor', 'affiliator', 'product'])
            ->where('status', '2')
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }


    public function AdminRequestActive()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->with(['vendor', 'affiliator', 'product'])
            ->where('status', '1')
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }


    function AdminRequestAll()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->with(['vendor', 'affiliator', 'product'])
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);

    }


    function RequestRejected()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->with(['vendor', 'affiliator', 'product'])
            ->where('status', '3')
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }


    function RequestUpdate(Request $request, $id)
    {

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


    function AdminRequestView($id)
    {
        $product = ProductDetails::with(['vendor', 'affiliator', 'product' => function ($query) {
            $query->with('productImage');
        }])->find($id);
        // 'sizes','colors'

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }


    public function AdminRequestBalances()
    {
        $user = User::where('balance_status', 0)->get();
        return response()->json($user);
    }


    public function AdminRequestBalanceActive()
    {
        $user = User::where('balance_status', 1)->get();
        return response()->json($user);
    }
}

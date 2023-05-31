<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductDetails;
use Illuminate\Http\Request;

class RequestProductController extends Controller
{
    //

    function RequestPending()
    {
        $product = ProductDetails::with(['product' => function ($query) {
            $query->with('productImage', 'sizes', 'colors');
        }])
            ->where('status', '2')
            ->where('vendor_id', auth()->user()->id)
            ->whereHas('product', function ($query) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            })
            ->with(['affiliator:id,name','vendor:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestView($id){
        $product = ProductDetails::with(['affiliator','vendor','product'=> function ($query) {
            $query->with('productImage', 'sizes', 'colors');
        }])
        ->where('vendor_id', auth()->user()->id)
        ->find($id);

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestActive()
    {

        $product = ProductDetails::with(['affiliator','vendor','product'])->where('status', '1')
            ->where('vendor_id', auth()->user()->id)
            ->whereHas('product', function ($query) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            })

            ->latest()->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestAll()
    {

        $product = ProductDetails::where('vendor_id', auth()->user()->id)
            ->with(['affiliator','vendor:id,name,image','product'])
            ->whereHas('product', function ($query) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            })

            ->latest()->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
        // return ProductDetails::all();
    }

    function RequestRejected()
    {
        $product = ProductDetails::with(['affiliator','vendor','product'])->where('status', '3')
            ->where('vendor_id', auth()->user()->id)
            ->whereHas('product', function ($query) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            })

            ->latest()->paginate(10)
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
}

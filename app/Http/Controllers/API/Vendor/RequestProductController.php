<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetails;
use Illuminate\Http\Request;

class RequestProductController extends Controller
{
    //

    function RequestPending()
    {

        // $product = ProductDetails::query()
        //     ->with(['product' => function ($query) {
        //         $query->select('id', 'name', 'selling_price')
        //             ->with('productImage');
        //     }])
        //     ->where('status', '2')
        //     ->where('vendor_id', auth()->user()->id)
        //     ->whereHas('product', function ($query) {
        //         $query->where('name', 'LIKE', '%' . request('search') . '%');
        //     })
        //     ->with(['affiliator:id,name', 'vendor:id,name'])
        //     ->withwhereHas('affiliator', function ($query) {
        //         $query->withCount(['affiliatoractiveproducts' => function ($query) {
        //             $query->where('status', 1);
        //         }])
        //             // ->with(['usersubscriptions' => function ($query) {
        //             //     $query->select('id', 'product_approve','user_id');
        //             // }])
        //             // ->where('affiliatoractiveproducts_count', '<', \DB::raw('usersubscriptions.product_approve'));
        //             ->withwhereHas('usersubscriptions', function ($query) {
        //                 $query->select('id', 'product_approve', 'user_id')
        //                     ->where('affiliatoractiveproducts_count', '<', 'product_approve');
        //             });
        //     })
        //     ->latest()
        //     ->paginate(10)
        //     ->withQueryString();


        $product = ProductDetails::query()
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'selling_price')
                    ->with('productImage');
            }])
            ->where('status', '2')
            ->where('vendor_id', auth()->user()->id)
            ->whereHas('product', function ($query) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            })
            ->with(['affiliator:id,name', 'vendor:id,name'])
            ->withwhereHas('affiliator', function ($query) {
                $query->withCount(['affiliatoractiveproducts' => function ($query) {
                    $query->where('status', 1);
                }])
                    // ->withSum('usersubscription','product_approve')
                    // ->having('affiliatoractiveproducts_count','<', \DB::raw('usersubscriptions_sum_product_approve') );
                    ->having('affiliatoractiveproducts_count','<', \DB::raw('usersubscription.product_approve') );
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestView($id)
    {
        $product = ProductDetails::with(['affiliator', 'vendor', 'product' => function ($query) {
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

        $product = ProductDetails::with(['affiliator', 'vendor', 'product'])->where('status', '1')
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
            ->with(['affiliator', 'vendor:id,name,image', 'product'])
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
        $product = ProductDetails::with(['affiliator', 'vendor', 'product'])->where('status', '3')
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

            if ($request->status == 1) {
                $getmembershipdetails = getmembershipdetails();

                $affiliaterequest = $getmembershipdetails?->affiliate_request;

                $totalrequest =  ProductDetails::where(['vendor_id' => userid(), 'status' => 1])->count();

                if (ismembershipexists() != 1) {
                    return responsejson('You do not have a membership', 'fail');
                }

                if (isactivemembership() != 1) {
                    return responsejson('Membership expired!', 'fail');
                }

                if ($affiliaterequest <=  $totalrequest) {
                    return responsejson('You can not accept product request more than ' . $affiliaterequest . '.', 'fail');
                }
            }


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

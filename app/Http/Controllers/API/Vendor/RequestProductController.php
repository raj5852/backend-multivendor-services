<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorProductrequestRequest;
use App\Models\Product;
use App\Models\ProductDetails;
use Illuminate\Http\Request;

class RequestProductController extends Controller
{


    function RequestPending()
    {

        $search = request('search');
        $product = ProductDetails::query()
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'selling_price')
                    ->with('productImage');
            }])
            ->where('vendor_id', auth()->user()->id)

            ->where('status', '2')
            ->whereHas('product')
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->with(['affiliator:id,name', 'vendor:id,name'])
            ->whereHas('affiliator', function ($query) {
                $query->withCount(['affiliatoractiveproducts' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    })
                    ->withSum('usersubscription', 'product_approve')
                    ->having('affiliatoractiveproducts_count', '<', \DB::raw('usersubscription_sum_product_approve'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function membershipexpireactiveproduct()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->withWhereHas('product' , function ($query) {
                $query->select('id', 'name', 'selling_price')
                    ->with('productImage');
            })
            ->where(['vendor_id', auth()->id(), 'status' => 1])
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->with(['affiliator:id,name', 'vendor:id,name'])
            ->whereHas('affiliator', function ($query) {
                $query->withCount(['affiliatoractiveproducts' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '<=', now());
                    });
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
        $product = ProductDetails::query()
            ->with(['affiliator', 'vendor', 'product' => function ($query) {
                $query->with('productImage');
            }])
            ->where('vendor_id', auth()->user()->id)
            ->whereHas('affiliator', function ($query) {
                $query->withCount(['affiliatoractiveproducts' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    })
                    ->withSum('usersubscription', 'product_approve')
                    ->having('affiliatoractiveproducts_count', '<', \DB::raw('usersubscription_sum_product_approve'));
            })
            ->find($id);
        if (!$product) {
            return $this->response('Not found');
        }

        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestActive()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'selling_price')
                    ->with('productImage');
            }])
            ->where('vendor_id', auth()->user()->id)
            ->where('status', 1)
            ->whereHas('product')
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->with(['affiliator:id,name', 'vendor:id,name'])
            ->whereHas('affiliator', function ($query) {
                $query->withCount(['affiliatoractiveproducts' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestAll()
    {
        $search = request('search');
        $product = ProductDetails::query()
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'selling_price')
                    ->with('productImage');
            }])
            ->where('vendor_id', auth()->user()->id)
            ->whereHas('product')
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->when(request('order_id'), fn ($q, $orderid) => $q->where('id', 'like', "%{$orderid}%"))
            ->with(['affiliator:id,name', 'vendor:id,name'])
            ->where(function ($query) {
                $query->whereHas('affiliator', function ($query) {
                    $query->withCount(['affiliatoractiveproducts' => function ($query) {
                        $query->where('status', 1);
                    }])
                        ->whereHas('usersubscription', function ($query) {
                            $query->where('expire_date', '>', now());
                        })
                        ->withSum('usersubscription', 'product_approve')
                        ->having('affiliatoractiveproducts_count', '<', \DB::raw('usersubscription_sum_product_approve'));
                });

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
            ->where(['vendor_id' => auth()->id(), 'status' => 3])
            ->withWhereHas('product' , function ($query) {
                $query->select('id', 'name', 'selling_price')
                    ->with('productImage');
            })
            ->when($search != '', function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhere('uniqid', 'like', '%' . $search . '%');
            })
            ->with(['affiliator:id,name', 'vendor:id,name'])
            ->whereHas('affiliator', function ($query) {
                $query->withCount(['affiliatoractiveproducts' => function ($query) {
                    $query->where('status', 1);
                }])
                    ->whereHas('usersubscription', function ($query) {
                        $query->where('expire_date', '>', now());
                    })
                    ->withSum('usersubscription', 'product_approve')
                    ->having('affiliatoractiveproducts_count', '<', \DB::raw('usersubscription_sum_product_approve'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();


        return response()->json([
            'status' => 200,
            'product' => $product,
        ]);
    }

    function RequestUpdate(VendorProductrequestRequest $request, $id)
    {
        $validatedData = $request->validated();
        $data =  ProductDetails::find($id);

        if ($data) {

            if (request('status') == 1) {
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


            $data->status = request('status');
            $data->reason = request('reason');
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

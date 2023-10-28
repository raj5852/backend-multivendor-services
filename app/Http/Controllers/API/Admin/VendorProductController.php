<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductEditRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class VendorProductController extends Controller
{
    function index()
    {
        return Product::query()
            ->with('vendor')
            ->whereHas('pendingproduct')
            ->latest()
            ->paginate(10);
    }


    function productview(int $id)
    {

        $product = Product::query()
            ->where('id', $id)
            ->withWhereHas('pendingproduct')
            ->first();
        if (!$product) {
            return responsejson('Not found', 'fail');
        }

        return $this->response($product);
    }

    function productstatus(ProductEditRequest $request, int $id){
        $product = Product::query()
        ->where('id', $id)
        ->with('pendingproduct')
        ->first();

        if(!$product){
            return responsejson('Not found','fail');
        }

        $data =  $product->pendingproduct;

        if(request('status') == 1){
            $data->is_reject = 1;
            $data->reason = request('reason');
            $data->save();
            // return
        }
        if(request('status') == 2){
            $product->short_description = $data->short_description;
            $product->short_description = $data->short_description;
        }


        return $this->response('Updated successfully');

    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponSendRequest;
use App\Http\Requests\CouponstausRequest;
use App\Models\CouponRequest;
use Illuminate\Http\Request;

class CouponRequestController extends Controller
{
    function store(CouponSendRequest $request)
    {
        CouponRequest::create([
            'user_id' => auth()->id(),
            'comments' => request('comments')
        ]);

        return $this->response('Coupon request send successfully!');
    }

    function getcouponrequest()
    {
        $couponrequest = CouponRequest::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if (!$couponrequest) {
            return responsejson('', 'success');
        }
        return $this->response($couponrequest);
    }

    function allcouponrequest()
    {
        return CouponRequest::with('user')
            ->latest()
            ->paginate(10);
    }

    function changestatus(CouponstausRequest $request, $id)
    {
        $couponrequest =  CouponRequest::find($id);
        if (!$couponrequest) {
            return responsejson('Not found', 'fail');
        }

        $couponrequest->status = request('status');
        if (request('status') == 'reject') {
            $couponrequest->reason = request('reason');
        }

        $couponrequest->save();
        return $this->response('success');
    }
}

<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\User;
use App\Services\Admin\CouponService;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data =  Coupon::latest()->with('user:id,name,email')->paginate();
        return $this->response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCouponRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCouponRequest $request)
    {
        $validatedData = $request->validated();

        CouponService::create($validatedData);

        return $this->response('Coupon created successfull!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return $this->response($coupon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCouponRequest  $request
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        if (!$coupon) {
            return responsejson('Not found', 'fail');
        }
        $validatedData = $request->validated();
        $coupon->update($validatedData);

        return $this->response('Updated Successfull');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        if (!$coupon) {
            return responsejson('Not found', 'fail');
        }
        $coupon->delete();

        return $this->response('Coupon deleted successfull');
    }

    function couponusers()
    {
        $data =  DB::table('users')->whereIn('role_as', [2, 3])->where('deleted_at', null)
            ->select('id', 'email')
            ->get();
        return $this->response($data);
    }
}

<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\SupportBox;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportBoxRequest;
use App\Http\Requests\UpdateSupportBoxRequest;

class SupportBoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSupportBoxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupportBoxRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupportBox  $supportBox
     * @return \Illuminate\Http\Response
     */
    public function show(SupportBox $supportBox)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSupportBoxRequest  $request
     * @param  \App\Models\SupportBox  $supportBox
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupportBoxRequest $request, SupportBox $supportBox)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupportBox  $supportBox
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupportBox $supportBox)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\RenewRequest;
use Illuminate\Http\Request;

class RenewController extends Controller
{
    function store(RenewRequest $request){
        return $request->validated();
    }
}

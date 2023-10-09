<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TestController extends Controller
{
    //

    function index(Request $request){
        $request->validate([
            'age'=>'required',
            'name'=> ['present_with:age',]
        ]);

        return 1;

    }
}

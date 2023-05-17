<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    //
    function index(){
        $withdraw  = Withdraw::latest()
        // ->when(request('search'),fn($q, $name)=>$q->where('name','like',"%{$name}%"))
        ->when(request('status') == 'pending', function ($q) {
            return $q->where('status', 'pending');
        })

        ->when(request('status') == 'active', function ($q) {
            return $q->where('status', 'active');
        })
        ->when(request('status') == 'rejected', function ($q) {
            return $q->where('status', 'rejected');
        })

        ->with('affiliator')
        ->paginate(10)
        ->withQueryString();

    }
}

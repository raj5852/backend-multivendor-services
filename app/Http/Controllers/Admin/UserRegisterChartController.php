<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserRegisterChartController extends Controller
{
    function index()
    {
        $startDate =   request('start') ? Carbon::parse(request('start')) : Carbon::now()->subDays(30);
        $endDate =   request('end') ? Carbon::parse(request('end')) : Carbon::now();

        $registrations = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as registrations')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($registrations);
    }
}

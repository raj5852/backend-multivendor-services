<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ApiAffiliatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (auth()->user()->role_as == '3') {
                if (auth()->user()->status == 'active') {
                    return $next($request);
                }else{
                    return response()->json([
                        'message' => 'Account is not active',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Access Denied.! As you are not an Affiliator.',
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please Login First.',
            ]);
        }
    }
}

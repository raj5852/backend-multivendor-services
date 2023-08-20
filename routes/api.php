<?php
use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;



//register
Route::post('register', [AuthController::class, 'Register']);
//login
Route::post('login', [AuthController::class, 'Login']);

Route::post('logout', [AuthController::class, 'logout']);




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('test',function(Request $request){

});

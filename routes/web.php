<?php

use App\Enums\Status;
use App\Models\AdminAdvertise;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return "API for SOS projec :)";
});

Auth::routes();


Route::get('migrate', function () {
    Artisan::call('migrate:fresh');
});


Route::get('seed', function () {
    Artisan::call('db:seed');
});



Route::get('demo', function () {

    return AdminAdvertise::all();


    $adminadvaertise = new AdminAdvertise();
    $adminadvaertise->trxid = 1;
    $adminadvaertise->campaign_objective   =  'test';
    $adminadvaertise->user_id  =  2;
    $adminadvaertise->campaign_name   =  'test';
    $adminadvaertise->conversion_location   =  'test';
    $adminadvaertise->performance_goal   =  'test';
    $adminadvaertise->budget_amount   =  'test';
    $adminadvaertise->start_date   =  'test';
    $adminadvaertise->end_date   =  'test';
    $adminadvaertise->age   = 'test';
    $adminadvaertise->gender   =  'test';
    $adminadvaertise->detail_targeting   =  'test';
    $adminadvaertise->country   =  'test';
    $adminadvaertise->city   =  'test';
    $adminadvaertise->device   =  'test';
    $adminadvaertise->platform   =  [
        [
            'asdf'=>'fasd',

        ],
        [
            'fasd'=>'fasd'
        ]

    ];
    $adminadvaertise->inventory   =  'test';
    $adminadvaertise->format   =  'test';
    $adminadvaertise->ad_creative   =  [
       [
        'name'=>'1'
       ],
       [
        'fas'=>'fasd'
       ]
    ];
    $adminadvaertise->budget   =  'test';
    $adminadvaertise->placements   =  'test';


    $adminadvaertise->destination   =  'test';
    $adminadvaertise->tracking   =  'test';
    $adminadvaertise->url_perimeter   =  'test';
    $adminadvaertise->number   =  'test';
    $adminadvaertise->last_description   =  'test';
    $adminadvaertise->status   =  Status::Pending->value;

    $adminadvaertise->save();

});
